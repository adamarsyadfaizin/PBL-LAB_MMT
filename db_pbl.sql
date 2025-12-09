--
-- PostgreSQL database dump
--

\restrict 2cDGLkgwc7x9kpfXb3dH0QEqzXi5EFH4zuybUopg2v0a6HxPg7rMorZjG5CecPh

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

-- Started on 2025-12-08 19:07:53

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 260 (class 1255 OID 99645)
-- Name: add_comment(character varying, integer, character varying, character varying, integer, text, integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.add_comment(IN p_entity_type character varying, IN p_entity_id integer, IN p_author_name character varying, IN p_author_email character varying, IN p_rating integer, IN p_content text, IN p_user_id integer DEFAULT NULL::integer)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_status VARCHAR(20);
    v_entity_exists BOOLEAN;
BEGIN
    -- Validasi rating
    IF p_rating IS NOT NULL AND (p_rating < 1 OR p_rating > 5) THEN
        RAISE EXCEPTION 'Rating must be between 1 and 5';
    END IF;
    
    -- Cek apakah entity exists
    IF p_entity_type = 'news' THEN
        SELECT EXISTS(SELECT 1 FROM news WHERE id = p_entity_id) INTO v_entity_exists;
    ELSIF p_entity_type = 'project' THEN
        SELECT EXISTS(SELECT 1 FROM projects WHERE id = p_entity_id) INTO v_entity_exists;
    ELSIF p_entity_type = 'media' THEN
        SELECT EXISTS(SELECT 1 FROM media_assets WHERE id = p_entity_id) INTO v_entity_exists;
    ELSE
        RAISE EXCEPTION 'Invalid entity type';
    END IF;
    
    IF NOT v_entity_exists THEN
        RAISE EXCEPTION 'Entity not found';
    END IF;
    
    -- Tentukan status (auto-approve jika ada user_id, pending jika tidak)
    IF p_user_id IS NOT NULL THEN
        v_status := 'approved';
    ELSE
        v_status := 'pending';
    END IF;
    
    -- Insert komentar
    INSERT INTO comments (
        entity_type, entity_id, author_name, author_email, 
        rating, content, status, user_id, created_at, updated_at
    ) VALUES (
        p_entity_type, p_entity_id, p_author_name, p_author_email,
        p_rating, p_content, v_status, p_user_id, NOW(), NOW()
    );
    
    -- Update rating entity
    IF p_rating IS NOT NULL AND v_status = 'approved' THEN
        CALL update_entity_rating(p_entity_type, p_entity_id);
    END IF;
    
    COMMIT;
END;
$$;


ALTER PROCEDURE public.add_comment(IN p_entity_type character varying, IN p_entity_id integer, IN p_author_name character varying, IN p_author_email character varying, IN p_rating integer, IN p_content text, IN p_user_id integer) OWNER TO postgres;

--
-- TOC entry 262 (class 1255 OID 99648)
-- Name: backup_lab_data(character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.backup_lab_data(IN p_backup_type character varying DEFAULT 'full'::character varying)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_backup_data JSONB;
    v_timestamp TEXT;
BEGIN
    v_timestamp := TO_CHAR(NOW(), 'YYYY-MM-DD_HH24-MI-SS');
    
    -- Kumpulkan data berdasarkan jenis backup
    IF p_backup_type = 'full' THEN
        SELECT jsonb_build_object(
            'timestamp', NOW(),
            'lab_profile', (SELECT json_agg(row_to_json(lp)) FROM lab_profile lp),
            'members', (SELECT json_agg(row_to_json(m)) FROM members m),
            'projects_summary', (SELECT json_agg(row_to_json(p)) FROM projects p WHERE status IN ('published', '1')),
            'news_summary', (SELECT json_agg(row_to_json(n)) FROM news n WHERE status = 'published'),
            'stats', (SELECT row_to_json(s) FROM mv_lab_dashboard_stats s)
        ) INTO v_backup_data;
        
    ELSIF p_backup_type = 'minimal' THEN
        SELECT jsonb_build_object(
            'timestamp', NOW(),
            'members_count', (SELECT COUNT(*) FROM members),
            'projects_count', (SELECT COUNT(*) FROM projects WHERE status IN ('published', '1')),
            'news_count', (SELECT COUNT(*) FROM news WHERE status = 'published'),
            'dashboard_stats', (SELECT row_to_json(s) FROM mv_lab_dashboard_stats s)
        ) INTO v_backup_data;
    END IF;
    
    -- Simpan ke file atau tabel backup (contoh: output ke log)
    RAISE NOTICE 'Backup created: %', v_backup_data;
    
    -- Bisa juga insert ke tabel backup jika ada
    -- INSERT INTO backup_logs (backup_type, data, created_at) 
    -- VALUES (p_backup_type, v_backup_data, NOW());
    
    COMMIT;
END;
$$;


ALTER PROCEDURE public.backup_lab_data(IN p_backup_type character varying) OWNER TO postgres;

--
-- TOC entry 263 (class 1255 OID 99649)
-- Name: cleanup_old_data(integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.cleanup_old_data(IN p_days_old integer DEFAULT 365)
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Archive dan hapus komentar spam/pending yang sangat lama
    DELETE FROM comments 
    WHERE status = 'pending' 
      AND created_at < NOW() - INTERVAL '30 days';
    
    -- Archive feedback yang sudah dibaca dan sangat lama
    DELETE FROM feedback 
    WHERE is_read = true 
      AND created_at < NOW() - INTERVAL '180 days';
    
    -- Non-aktifkan proyek/berita yang sangat lama dan tidak aktif
    UPDATE projects 
    SET status = 'archived' 
    WHERE status = '1' 
      AND updated_at < NOW() - (p_days_old || ' days')::INTERVAL;
    
    UPDATE news 
    SET status = 'archived' 
    WHERE status = 'published' 
      AND updated_at < NOW() - (p_days_old || ' days')::INTERVAL;
    
    -- Refresh materialized views
    PERFORM refresh_lab_dashboard_stats();
    
    COMMIT;
    
    RAISE NOTICE 'Cleanup completed for data older than % days', p_days_old;
END;
$$;


ALTER PROCEDURE public.cleanup_old_data(IN p_days_old integer) OWNER TO postgres;

--
-- TOC entry 261 (class 1255 OID 99646)
-- Name: generate_monthly_report(date); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.generate_monthly_report(IN p_month date DEFAULT NULL::date)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_report_month DATE;
    v_total_news INTEGER;
    v_total_projects INTEGER;
    v_total_comments INTEGER;
    v_total_feedback INTEGER;
    v_report_text TEXT;
BEGIN
    -- Jika bulan tidak ditentukan, gunakan bulan sebelumnya
    IF p_month IS NULL THEN
        v_report_month := DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month');
    ELSE
        v_report_month := DATE_TRUNC('month', p_month);
    END IF;
    
    -- Hitung statistik
    SELECT COUNT(*) INTO v_total_news
    FROM news 
    WHERE DATE_TRUNC('month', created_at) = v_report_month 
      AND status = 'published';
    
    SELECT COUNT(*) INTO v_total_projects
    FROM projects 
    WHERE DATE_TRUNC('month', created_at) = v_report_month 
      AND status IN ('published', '1');
    
    SELECT COUNT(*) INTO v_total_comments
    FROM comments 
    WHERE DATE_TRUNC('month', created_at) = v_report_month 
      AND status = 'approved';
    
    SELECT COUNT(*) INTO v_total_feedback
    FROM feedback 
    WHERE DATE_TRUNC('month', created_at) = v_report_month;
    
    -- Generate report text
    v_report_text := format(
        'Laporan Aktivitas Lab - %s
        =================================
        Total Berita/Kegiatan: %s
        Total Proyek Baru: %s
        Total Komentar Disetujui: %s
        Total Feedback Masuk: %s
        
        Periode: %s sampai %s',
        TO_CHAR(v_report_month, 'Month YYYY'),
        v_total_news,
        v_total_projects,
        v_total_comments,
        v_total_feedback,
        v_report_month,
        v_report_month + INTERVAL '1 month' - INTERVAL '1 day'
    );
    
    -- Tampilkan report (bisa disimpan ke tabel lain jika perlu)
    RAISE NOTICE '%', v_report_text;
    
    -- Refresh materialized views
    REFRESH MATERIALIZED VIEW CONCURRENTLY mv_monthly_activity;
    REFRESH MATERIALIZED VIEW CONCURRENTLY mv_lab_dashboard_stats;
END;
$$;


ALTER PROCEDURE public.generate_monthly_report(IN p_month date) OWNER TO postgres;

--
-- TOC entry 248 (class 1255 OID 99653)
-- Name: get_lab_stats(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.get_lab_stats() RETURNS TABLE(total_news bigint, total_projects bigint, total_members bigint, total_comments bigint, total_unread_feedback bigint)
    LANGUAGE plpgsql STABLE
    AS $$
BEGIN
    RETURN QUERY 
    SELECT 
        s.total_news,
        s.total_projects,
        s.total_members,
        s.total_comments_approved,
        s.total_unread_feedback
    FROM mv_lab_dashboard_stats s;
END;
$$;


ALTER FUNCTION public.get_lab_stats() OWNER TO postgres;

--
-- TOC entry 246 (class 1255 OID 99619)
-- Name: refresh_lab_dashboard_stats(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.refresh_lab_dashboard_stats() RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    REFRESH MATERIALIZED VIEW CONCURRENTLY mv_lab_dashboard_stats;
END;
$$;


ALTER FUNCTION public.refresh_lab_dashboard_stats() OWNER TO postgres;

--
-- TOC entry 247 (class 1255 OID 99644)
-- Name: update_entity_rating(character varying, integer); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.update_entity_rating(IN p_entity_type character varying, IN p_entity_id integer)
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_avg_rating DECIMAL(3,2);
    v_entity_table VARCHAR(50);
BEGIN
    -- Hitung rating rata-rata dari komentar yang approved
    SELECT AVG(rating) INTO v_avg_rating
    FROM comments
    WHERE entity_type = p_entity_type 
      AND entity_id = p_entity_id 
      AND status = 'approved'
      AND rating IS NOT NULL;
    
    -- Update rating di tabel yang sesuai
    IF p_entity_type = 'project' THEN
        UPDATE projects 
        SET rating = ROUND(v_avg_rating) 
        WHERE id = p_entity_id;
    ELSIF p_entity_type = 'media' THEN
        UPDATE media_assets 
        SET rating = ROUND(v_avg_rating) 
        WHERE id = p_entity_id;
    END IF;
    
    -- Refresh materialized views yang terkait
    PERFORM refresh_lab_dashboard_stats();
END;
$$;


ALTER PROCEDURE public.update_entity_rating(IN p_entity_type character varying, IN p_entity_id integer) OWNER TO postgres;

--
-- TOC entry 264 (class 1255 OID 99382)
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_updated_at_column() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 214 (class 1259 OID 99383)
-- Name: activity_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.activity_logs (
    id integer NOT NULL,
    username character varying(50),
    action character varying(255),
    ip_address character varying(45),
    device_info text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.activity_logs OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 99389)
-- Name: activity_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.activity_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.activity_logs_id_seq OWNER TO postgres;

--
-- TOC entry 3562 (class 0 OID 0)
-- Dependencies: 215
-- Name: activity_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.activity_logs_id_seq OWNED BY public.activity_logs.id;


--
-- TOC entry 216 (class 1259 OID 99390)
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 99393)
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.categories_id_seq OWNER TO postgres;

--
-- TOC entry 3563 (class 0 OID 0)
-- Dependencies: 217
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- TOC entry 218 (class 1259 OID 99394)
-- Name: comments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.comments (
    id integer NOT NULL,
    entity_type character varying(50) NOT NULL,
    entity_id integer NOT NULL,
    author_name character varying(100) NOT NULL,
    author_email character varying(255),
    rating integer,
    content text NOT NULL,
    status character varying(20) DEFAULT 'pending'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    user_id integer,
    CONSTRAINT comments_rating_check CHECK (((rating IS NULL) OR ((rating >= 1) AND (rating <= 5))))
);


ALTER TABLE public.comments OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 99403)
-- Name: comments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.comments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.comments_id_seq OWNER TO postgres;

--
-- TOC entry 3564 (class 0 OID 0)
-- Dependencies: 219
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.comments_id_seq OWNED BY public.comments.id;


--
-- TOC entry 220 (class 1259 OID 99404)
-- Name: feedback; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.feedback (
    id integer NOT NULL,
    nama_lengkap character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    subjek character varying(255) NOT NULL,
    pesan text NOT NULL,
    created_at timestamp without time zone DEFAULT now(),
    is_read boolean DEFAULT false
);


ALTER TABLE public.feedback OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 99411)
-- Name: feedback_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.feedback_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.feedback_id_seq OWNER TO postgres;

--
-- TOC entry 3565 (class 0 OID 0)
-- Dependencies: 221
-- Name: feedback_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.feedback_id_seq OWNED BY public.feedback.id;


--
-- TOC entry 222 (class 1259 OID 99412)
-- Name: lab_profile; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lab_profile (
    id bigint NOT NULL,
    visi text,
    misi text,
    sejarah text,
    updated_at timestamp with time zone DEFAULT now(),
    alamat_lab text,
    email_lab character varying(255),
    telepon_lab character varying(50),
    lokasi_lab text,
    fb_link character varying(255),
    x_link character varying(255),
    ig_link character varying(255),
    yt_link character varying(255),
    linkedin character varying(255),
    logo_path character varying(255),
    hero_image_path character varying(255),
    hero_title character varying(255),
    hero_description text,
    about_hero_image character varying(255) DEFAULT 'assets/images/hero.jpg'::character varying,
    news_hero_image character varying(255) DEFAULT 'assets/images/hero.jpg'::character varying,
    project_hero_image character varying(255) DEFAULT 'assets/images/hero.jpg'::character varying,
    gallery_hero_image character varying(255) DEFAULT 'assets/images/hero.jpg'::character varying,
    contact_hero_image character varying(255) DEFAULT 'assets/images/hero.jpg'::character varying,
    about_title character varying(255) DEFAULT 'Profil Laboratorium'::character varying,
    news_title character varying(255) DEFAULT 'Berita & Kegiatan'::character varying,
    project_title character varying(255) DEFAULT 'Katalog Proyek'::character varying,
    gallery_title character varying(255) DEFAULT 'Galeri Multimedia'::character varying,
    contact_title character varying(255) DEFAULT 'Kontak Kami'::character varying,
    footer_desc text,
    copyright_text character varying(255) DEFAULT NULL::character varying,
    struktur_org_path character varying(255)
);


ALTER TABLE public.lab_profile OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 99429)
-- Name: lab_profile_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.lab_profile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lab_profile_id_seq OWNER TO postgres;

--
-- TOC entry 3566 (class 0 OID 0)
-- Dependencies: 223
-- Name: lab_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lab_profile_id_seq OWNED BY public.lab_profile.id;


--
-- TOC entry 224 (class 1259 OID 99430)
-- Name: media_assets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.media_assets (
    id bigint NOT NULL,
    type character varying(50),
    url character varying(2048) NOT NULL,
    caption character varying(512),
    created_at timestamp with time zone DEFAULT now(),
    deskripsi text,
    rating integer,
    event_name character varying(255),
    CONSTRAINT media_assets_rating_check CHECK (((rating IS NULL) OR ((rating >= 1) AND (rating <= 5))))
);


ALTER TABLE public.media_assets OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 99437)
-- Name: media_assets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.media_assets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.media_assets_id_seq OWNER TO postgres;

--
-- TOC entry 3567 (class 0 OID 0)
-- Dependencies: 225
-- Name: media_assets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.media_assets_id_seq OWNED BY public.media_assets.id;


--
-- TOC entry 226 (class 1259 OID 99438)
-- Name: members; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.members (
    id bigint NOT NULL,
    name character varying(200) NOT NULL,
    role character varying(150),
    avatar_url character varying(1024),
    linkedin_url character varying(1024),
    tags character varying(255),
    scholar_url character varying(1024),
    youtube character varying(255),
    facebook character varying(255),
    instagram character varying(255)
);


ALTER TABLE public.members OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 99443)
-- Name: members_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.members_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.members_id_seq OWNER TO postgres;

--
-- TOC entry 3568 (class 0 OID 0)
-- Dependencies: 227
-- Name: members_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.members_id_seq OWNED BY public.members.id;


--
-- TOC entry 228 (class 1259 OID 99444)
-- Name: mv_feedback_summary; Type: MATERIALIZED VIEW; Schema: public; Owner: postgres
--

CREATE MATERIALIZED VIEW public.mv_feedback_summary AS
 SELECT feedback.id,
    feedback.nama_lengkap,
    feedback.email,
    feedback.subjek,
    "left"(feedback.pesan, 50) AS pesan_preview,
    feedback.created_at,
    feedback.is_read,
    EXTRACT(epoch FROM feedback.created_at) AS created_epoch
   FROM public.feedback
  ORDER BY feedback.created_at DESC
  WITH NO DATA;


ALTER TABLE public.mv_feedback_summary OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 99450)
-- Name: news; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.news (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    summary character varying(512),
    content text,
    cover_image character varying(1024),
    status character varying(20) DEFAULT 1,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    user_id integer,
    type character varying(20) DEFAULT 'news'::character varying,
    category character varying(100)
);


ALTER TABLE public.news OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 99472)
-- Name: projects; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.projects (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    summary character varying(512),
    description text,
    year integer,
    cover_image character varying(1024),
    repo_url character varying(1024),
    demo_url character varying(1024),
    status character varying(50) DEFAULT 1,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    category_id bigint,
    user_id integer,
    rating integer,
    CONSTRAINT projects_rating_check CHECK (((rating IS NULL) OR ((rating >= 1) AND (rating <= 5))))
);


ALTER TABLE public.projects OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 99486)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(200) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    role character varying(50) DEFAULT 'user'::character varying NOT NULL,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now()
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 99613)
-- Name: mv_lab_dashboard_stats; Type: MATERIALIZED VIEW; Schema: public; Owner: postgres
--

CREATE MATERIALIZED VIEW public.mv_lab_dashboard_stats AS
 SELECT ( SELECT count(*) AS count
           FROM public.news
          WHERE ((news.status)::text = 'published'::text)) AS total_news,
    ( SELECT count(*) AS count
           FROM public.news
          WHERE (((news.type)::text = 'kegiatan'::text) AND ((news.status)::text = 'published'::text))) AS total_kegiatan,
    ( SELECT count(*) AS count
           FROM public.projects
          WHERE ((projects.status)::text = ANY ((ARRAY['published'::character varying, '1'::character varying])::text[]))) AS total_projects,
    ( SELECT count(DISTINCT projects.year) AS count
           FROM public.projects
          WHERE (projects.year IS NOT NULL)) AS total_years_active,
    ( SELECT count(*) AS count
           FROM public.members) AS total_members,
    ( SELECT count(*) AS count
           FROM public.media_assets) AS total_media,
    ( SELECT count(*) AS count
           FROM public.media_assets
          WHERE ((media_assets.type)::text = 'video'::text)) AS total_videos,
    ( SELECT count(*) AS count
           FROM public.comments
          WHERE ((comments.status)::text = 'approved'::text)) AS total_comments_approved,
    ( SELECT count(*) AS count
           FROM public.feedback
          WHERE (feedback.is_read = false)) AS total_unread_feedback,
    ( SELECT count(*) AS count
           FROM public.users
          WHERE ((users.role)::text = 'anggota'::text)) AS total_anggota_users,
    ( SELECT count(*) AS count
           FROM public.users
          WHERE ((users.role)::text = 'admin'::text)) AS total_admin_users
  WITH NO DATA;


ALTER TABLE public.mv_lab_dashboard_stats OWNER TO postgres;

--
-- TOC entry 243 (class 1259 OID 99620)
-- Name: mv_monthly_activity; Type: MATERIALIZED VIEW; Schema: public; Owner: postgres
--

CREATE MATERIALIZED VIEW public.mv_monthly_activity AS
 SELECT date_trunc('month'::text, comments.created_at) AS month,
    count(*) FILTER (WHERE ((comments.entity_type)::text = 'news'::text)) AS news_comments,
    count(*) FILTER (WHERE ((comments.entity_type)::text = 'media'::text)) AS media_comments,
    count(*) FILTER (WHERE (comments.entity_type IS NULL)) AS other_comments,
    count(*) AS total_comments
   FROM public.comments
  WHERE ((comments.status)::text = 'approved'::text)
  GROUP BY (date_trunc('month'::text, comments.created_at))
  ORDER BY (date_trunc('month'::text, comments.created_at)) DESC
  WITH NO DATA;


ALTER TABLE public.mv_monthly_activity OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 99460)
-- Name: news_tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.news_tags (
    id integer NOT NULL,
    news_id integer NOT NULL,
    tag_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.news_tags OWNER TO postgres;

--
-- TOC entry 245 (class 1259 OID 99635)
-- Name: mv_news_with_stats; Type: MATERIALIZED VIEW; Schema: public; Owner: postgres
--

CREATE MATERIALIZED VIEW public.mv_news_with_stats AS
 SELECT n.id,
    n.title,
    n.slug,
    n.summary,
    n.category,
    n.cover_image,
    n.status,
    n.created_at,
    n.updated_at,
    n.type,
    count(DISTINCT c.id) AS total_comments,
    COALESCE(avg(c.rating), (0)::numeric) AS avg_rating,
    count(DISTINCT nt.tag_id) AS total_tags,
    u.name AS author_name
   FROM (((public.news n
     LEFT JOIN public.comments c ON ((((c.entity_type)::text = 'news'::text) AND (c.entity_id = n.id) AND ((c.status)::text = 'approved'::text))))
     LEFT JOIN public.news_tags nt ON ((n.id = nt.news_id)))
     LEFT JOIN public.users u ON ((n.user_id = u.id)))
  WHERE ((n.status)::text = 'published'::text)
  GROUP BY n.id, n.title, n.slug, n.summary, n.category, n.cover_image, n.status, n.created_at, n.updated_at, n.type, u.name
  WITH NO DATA;


ALTER TABLE public.mv_news_with_stats OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 99465)
-- Name: project_members; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_members (
    id integer NOT NULL,
    project_id integer NOT NULL,
    member_id integer NOT NULL
);


ALTER TABLE public.project_members OWNER TO postgres;

--
-- TOC entry 244 (class 1259 OID 99625)
-- Name: mv_project_details; Type: MATERIALIZED VIEW; Schema: public; Owner: postgres
--

CREATE MATERIALIZED VIEW public.mv_project_details AS
 SELECT p.id,
    p.title,
    p.slug,
    p.summary,
    p.year,
    p.cover_image,
    p.repo_url,
    p.demo_url,
    p.status,
    p.created_at,
    p.rating,
    c.name AS category_name,
    c.slug AS category_slug,
    count(DISTINCT pm.member_id) AS total_members,
    count(DISTINCT cm.id) AS total_comments,
    COALESCE(avg(cm.rating), (0)::numeric) AS avg_rating
   FROM (((public.projects p
     LEFT JOIN public.categories c ON ((p.category_id = c.id)))
     LEFT JOIN public.project_members pm ON ((p.id = pm.project_id)))
     LEFT JOIN public.comments cm ON ((((cm.entity_type)::text = 'project'::text) AND (cm.entity_id = p.id) AND ((cm.status)::text = 'approved'::text))))
  GROUP BY p.id, p.title, p.slug, p.summary, p.year, p.cover_image, p.repo_url, p.demo_url, p.status, p.created_at, p.rating, c.name, c.slug
  WITH NO DATA;


ALTER TABLE public.mv_project_details OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 99459)
-- Name: news_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.news_id_seq OWNER TO postgres;

--
-- TOC entry 3569 (class 0 OID 0)
-- Dependencies: 230
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.news_id_seq OWNED BY public.news.id;


--
-- TOC entry 232 (class 1259 OID 99464)
-- Name: news_tags_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.news_tags_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.news_tags_id_seq OWNER TO postgres;

--
-- TOC entry 3570 (class 0 OID 0)
-- Dependencies: 232
-- Name: news_tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.news_tags_id_seq OWNED BY public.news_tags.id;


--
-- TOC entry 234 (class 1259 OID 99468)
-- Name: project_members_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.project_members_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_members_id_seq OWNER TO postgres;

--
-- TOC entry 3571 (class 0 OID 0)
-- Dependencies: 234
-- Name: project_members_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_members_id_seq OWNED BY public.project_members.id;


--
-- TOC entry 235 (class 1259 OID 99469)
-- Name: project_tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_tags (
    project_id bigint NOT NULL,
    tag_id bigint NOT NULL
);


ALTER TABLE public.project_tags OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 99481)
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO postgres;

--
-- TOC entry 3572 (class 0 OID 0)
-- Dependencies: 237
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.projects_id_seq OWNED BY public.projects.id;


--
-- TOC entry 238 (class 1259 OID 99482)
-- Name: tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tags (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.tags OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 99485)
-- Name: tags_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tags_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tags_id_seq OWNER TO postgres;

--
-- TOC entry 3573 (class 0 OID 0)
-- Dependencies: 239
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tags_id_seq OWNED BY public.tags.id;


--
-- TOC entry 241 (class 1259 OID 99494)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 3574 (class 0 OID 0)
-- Dependencies: 241
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3265 (class 2604 OID 99495)
-- Name: activity_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs ALTER COLUMN id SET DEFAULT nextval('public.activity_logs_id_seq'::regclass);


--
-- TOC entry 3267 (class 2604 OID 99496)
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- TOC entry 3268 (class 2604 OID 99497)
-- Name: comments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments ALTER COLUMN id SET DEFAULT nextval('public.comments_id_seq'::regclass);


--
-- TOC entry 3272 (class 2604 OID 99498)
-- Name: feedback id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.feedback ALTER COLUMN id SET DEFAULT nextval('public.feedback_id_seq'::regclass);


--
-- TOC entry 3275 (class 2604 OID 99499)
-- Name: lab_profile id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile ALTER COLUMN id SET DEFAULT nextval('public.lab_profile_id_seq'::regclass);


--
-- TOC entry 3288 (class 2604 OID 99500)
-- Name: media_assets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.media_assets ALTER COLUMN id SET DEFAULT nextval('public.media_assets_id_seq'::regclass);


--
-- TOC entry 3290 (class 2604 OID 99501)
-- Name: members id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.members ALTER COLUMN id SET DEFAULT nextval('public.members_id_seq'::regclass);


--
-- TOC entry 3291 (class 2604 OID 99502)
-- Name: news id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news ALTER COLUMN id SET DEFAULT nextval('public.news_id_seq'::regclass);


--
-- TOC entry 3296 (class 2604 OID 99503)
-- Name: news_tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags ALTER COLUMN id SET DEFAULT nextval('public.news_tags_id_seq'::regclass);


--
-- TOC entry 3298 (class 2604 OID 99504)
-- Name: project_members id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members ALTER COLUMN id SET DEFAULT nextval('public.project_members_id_seq'::regclass);


--
-- TOC entry 3299 (class 2604 OID 99505)
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects ALTER COLUMN id SET DEFAULT nextval('public.projects_id_seq'::regclass);


--
-- TOC entry 3303 (class 2604 OID 99506)
-- Name: tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags ALTER COLUMN id SET DEFAULT nextval('public.tags_id_seq'::regclass);


--
-- TOC entry 3304 (class 2604 OID 99507)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3525 (class 0 OID 99383)
-- Dependencies: 214
-- Data for Name: activity_logs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.activity_logs (id, username, action, ip_address, device_info, created_at) FROM stdin;
1	Admin	Update Profil Lab	::1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36	2025-12-07 22:57:05.635303
2	admin2	Login Berhasil	::1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36	2025-12-08 07:39:26.92786
\.


--
-- TOC entry 3527 (class 0 OID 99390)
-- Dependencies: 216
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categories (id, name, slug) FROM stdin;
1	Mobile Development	mobile-development
2	Multimedia	multimedia
3	Artificial Intelligence	ai
4	Internet of Things	iot
5	UI/UX Design	ui-ux
6	Mobile App	mobile-app
7	Game Development	game-development
8	AR/VR	ar-vr
9	Animation	animation
10	Berita	berita
11	Kegiatan	kegiatan
12	Workshop	workshop
\.


--
-- TOC entry 3529 (class 0 OID 99394)
-- Dependencies: 218
-- Data for Name: comments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.comments (id, entity_type, entity_id, author_name, author_email, rating, content, status, created_at, updated_at, user_id) FROM stdin;
4	news	7	serigala		2	Halo tes tes tes tes	approved	2025-12-01 11:11:37.361597	2025-12-01 11:11:37.361597	\N
5	news	6	serigala		5	Halo halo halo jjjjjjj	approved	2025-12-01 11:12:21.685575	2025-12-01 11:12:21.685575	\N
6	media	7	serigala		1	Sangat bagus ekali woow wow wow	approved	2025-12-01 11:24:18.103887	2025-12-01 11:24:18.103887	\N
7	media	7	serigala		3	tessssssss sbbsbsbsbbs	approved	2025-12-01 13:06:27.53489	2025-12-01 13:06:27.53489	\N
8	news	7	admin2		3	tes vvvvvvvvvvvvvv	approved	2025-12-01 14:11:42.046448	2025-12-01 14:11:42.046448	\N
9	news	7	serigala		3	full margin	approved	2025-12-02 15:23:28.5802	2025-12-02 15:23:28.5802	\N
\.


--
-- TOC entry 3531 (class 0 OID 99404)
-- Dependencies: 220
-- Data for Name: feedback; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.feedback (id, nama_lengkap, email, subjek, pesan, created_at, is_read) FROM stdin;
78	Raihan	bujexixi@gmal.com	testing	Haiii aku raihan	2025-12-08 07:48:47.582192	f
\.


--
-- TOC entry 3533 (class 0 OID 99412)
-- Dependencies: 222
-- Data for Name: lab_profile; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lab_profile (id, visi, misi, sejarah, updated_at, alamat_lab, email_lab, telepon_lab, lokasi_lab, fb_link, x_link, ig_link, yt_link, linkedin, logo_path, hero_image_path, hero_title, hero_description, about_hero_image, news_hero_image, project_hero_image, gallery_hero_image, contact_hero_image, about_title, news_title, project_title, gallery_title, contact_title, footer_desc, copyright_text, struktur_org_path) FROM stdin;
2	Menjadi laboratorium multimedia dan mobile technology terdepan dalam inovasi dan kreativitas digital	Mengembangkan solusi digital inovatif melalui penelitian, pengembangan, dan kolaborasi dengan industri	Laboratorium Multimedia dan Mobile Tech didirikan pada tahun 2010 sebagai pusat pengembangan teknologi digital di Politeknik Negeri Malang	2025-11-17 13:52:20.720474+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	Profil Laboratorium	Berita & Kegiatan	Katalog Proyek	Galeri Multimedia	Kontak Kami	\N	\N	assets/uploads/struktur_1765123025.png
1	Menjadi laboratorium multimedia dan mobile technology terdepan dalam inovasi dan kreativitas digital	Mengembangkan solusi digital inovatif melalui penelitian, pengembangan, dan kolaborasi dengan industri	Laboratorium Multimedia dan Mobile Tech didirikan pada tahun 2010 sebagai pusat pengembangan teknologi digital di Politeknik Negeri Malang	2025-11-18 10:58:08.336706+07	Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141	multimedia@gmail.com	(0341) 404424	Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Malang	https://facebook.com/polinema.mobilelab	https://twitter.com/polinema_mobile	https://instagram.com/polinema.mobilelab	https://youtube.com/c/PolinemaMobileLab	https://linkedin.com/company/polinema-mobile-lab	assets/images/logo-placeholder.png	assets/images/hero.jpg	LABORATORIUM MOBILE AND MULTIMEDIA TECH	Pusat pengembangan karya inovatif di bidang UI/UX, Game, AR/VR.	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	Profil Laboratorium	Berita & Kegiatan	Katalog Proyek	Galeri Multimedia	Kontak Kami	\N	\N	assets/uploads/struktur_1765123025.png
\.


--
-- TOC entry 3535 (class 0 OID 99430)
-- Dependencies: 224
-- Data for Name: media_assets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.media_assets (id, type, url, caption, created_at, deskripsi, rating, event_name) FROM stdin;
10	foto	assets/uploads/1765094545_6935349121048.jpeg	Pengembangan Aplikasi Mobile	2025-12-07 15:02:25.138516+07	Dokumentasi kegiatan pembuatan aplikasi Android/iOS menggunakan Flutter, Kotlin, atau Java.	\N	Mobile App Development Showcase
12	video	assets/uploads/1765103096_693555f856c25.mp4	UI/UX Design Bootcamp	2025-12-01 12:00:13+07	Aktivitas perancangan antarmuka mobile menggunakan Figma, lengkap dengan prototyping	\N	Figma Training
11	foto	assets/uploads/1765102640_69355430be4a3.webp	Mobile Game Development	2025-11-11 12:00:22+07	Foto  pembuatan game 2D/3D menggunakan Unity yang cocok untuk platform mobile	\N	Global Game Jam
13	foto	assets/uploads/1765113355_69357e0b5abb1.jpg	Multimedia Creation Session	2025-12-03 13:15:55+07	Kegiatan editing video, efek visual, audio mixing, atau desain grafis	\N	Editing & Digital Art Workshop
14	foto	assets/uploads/1765113899_6935802b55a81.jpg	AR for Education	2025-10-15 13:24:59+07	Demo aplikasi AR berbasis Android seperti marker tracking, objek 3D, dan AR edukasi.	\N	AR Exhibition Day
15	foto	assets/uploads/1765114091_693580ebe72e2.jpeg	Virtual Reality Experience	2025-12-04 13:28:11+07	Dokumentasi uji coba VR headset dan demo aplikasi VR untuk pembelajaran.	\N	VR Technology Showcase
16	animasi	assets/uploads/1765155226_6936219a7804b.gif	Animation Project Showcase	2025-12-02 00:53:46+07	Galeri hasil render animasi karakter, motion graphics, atau animasi edukasi.	\N	2D/3D Animation Class
\.


--
-- TOC entry 3537 (class 0 OID 99438)
-- Dependencies: 226
-- Data for Name: members; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.members (id, name, role, avatar_url, linkedin_url, tags, scholar_url, youtube, facebook, instagram) FROM stdin;
4	Dewi Lestari	Sekretaris	../assets/images/team/dewi.jpg	https://linkedin.com/in/dewilestari	Sekretaris	https://scholar.google.com/citations?user=delta999	https://youtube.com/@deltastream	https://facebook.com/delta.user	https://instagram.com/delta.snap
3	Fajar Pratama	Anggota	../assets/images/team/fajar.jpg	https://linkedin.com/in/fajarpratama	Anggota	https://scholar.google.com/citations?user=gamma789	https://youtube.com/@gammaworld	https://facebook.com/gamma.page	https://instagram.com/gamma.life
2	Nadia Putri	Wakil Ketua	../assets/images/team/nadia.jpg	https://linkedin.com/in/nadiaputri	Wakil	https://scholar.google.com/citations?user=beta456	https://youtube.com/@betachannel	https://facebook.com/beta.profile	https://instagram.com/beta.ig
1	Rizky Ananda	Ketua Laboratorium	../assets/images/team/rizky.jpg	https://linkedin.com/in/rizkyananda	Ketua	https://scholar.google.com/citations?user=alpha123	https://youtube.com/@useralpha	https://facebook.com/user.alpha	https://instagram.com/user.alpha
\.


--
-- TOC entry 3540 (class 0 OID 99450)
-- Dependencies: 229
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.news (id, title, slug, summary, content, cover_image, status, created_at, updated_at, user_id, type, category) FROM stdin;
10	KULIAH TAMU "BUSINESSES & BIG DATA"	kuliah-tamu-businesses-big-data	Jurusan TI di Polinema mengadakan kuliah tamu BUSINESSES & BIG DATA	Jurusan TI di Polinema mengadakan kuliah tamu BUSINESSES & BIG DATA	assets/images/news/1764700405_845.jpg	published	2025-12-03 07:19:17+07	2025-12-07 14:19:17.324301+07	\N	news	kegiatan
9	Persiapan menuju GEMASTIK 2025	persiapan-menuju-gemastik-2025	Persiapan mahasiswa menuju GEMASTIK 2025	Persiapan mahasiswa menuju GEMASTIK 2025	assets/images/news/1764700166_573.jpg	published	2025-12-03 07:19:26+07	2025-12-07 14:19:26.260841+07	\N	news	berita
11	Pelatihan IOT	pelatihan-iot	Pelatihan IOT	Pelatihan IOT untuk Mahasiswa	assets/images/news/1764700442_186.jpg	published	2025-12-01 07:20:45+07	2025-12-07 14:20:45.702673+07	\N	news	workshop
8	Juara Gemastik 2024	juara-gemastik-2024	Sejumlah Mahasiswa Meraih penghargaan di ajang GEMASTIK 2024	Mahasiswa Meraih Penghargaan di ajang GEMASTIK 2024	assets/images/news/1764699982_428.jpeg	published	2025-12-01 07:21:06+07	2025-12-07 14:21:06.410781+07	\N	news	lomba
12	WORKSHOP UI/UX	workshop-ui-ux	WORKSHOP UI/UX	Mahasiswa mengikuti Workshop UI/UX yg diadakan oleh jurusan	assets/images/news/1764701741_699.jpg	published	2025-12-03 07:19:09+07	2025-12-07 14:19:09.174126+07	\N	news	workshop
\.


--
-- TOC entry 3542 (class 0 OID 99460)
-- Dependencies: 231
-- Data for Name: news_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.news_tags (id, news_id, tag_id, created_at) FROM stdin;
\.


--
-- TOC entry 3544 (class 0 OID 99465)
-- Dependencies: 233
-- Data for Name: project_members; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_members (id, project_id, member_id) FROM stdin;
\.


--
-- TOC entry 3546 (class 0 OID 99469)
-- Dependencies: 235
-- Data for Name: project_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_tags (project_id, tag_id) FROM stdin;
\.


--
-- TOC entry 3547 (class 0 OID 99472)
-- Dependencies: 236
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.projects (id, title, slug, summary, description, year, cover_image, repo_url, demo_url, status, created_at, updated_at, category_id, user_id, rating) FROM stdin;
9	Sistem AI Pantau Lalu Lintas di Persimpangan Kota	sistem-ai-pantau-lalu-lintas-di-persimpangan-kota	Dinas Perhubungan sebuah kota besar menerapkan teknologi computer vision berbasis kecerdasan buatan untuk memantau kepadatan lalu lintas secara real-time.	Dinas Perhubungan sebuah kota besar menerapkan teknologi computer vision berbasis kecerdasan buatan untuk memantau kepadatan lalu lintas secara real-time. Kamera yang dipasang pada persimpangan utama mampu mendeteksi dan mengklasifikasikan kendaraan secara otomatis melalui kotak hijau yang muncul pada layar. Teknologi ini digunakan untuk mengurangi kemacetan, melakukan analisis pola kendaraan, serta mendukung sistem pengaturan lampu lalu lintas adaptif.	2025	assets/images/projects/1765084591_551.jpg	\N	\N	published	2025-12-07 12:16:31.256679+07	2025-12-07 12:16:31.256679+07	3	\N	\N
10	Analisis Kompetisi Marketplace Indonesia 2024	analisis-kompetisi-marketplace-indonesia-2024	Proyek ini mengkaji persaingan antara marketplace besar di Indonesia, menilai strategi, fitur, dan perilaku konsumen dalam dunia e-commerce yang semakin kompetitif.	Proyek ini berfokus pada analisis persaingan berbagai marketplace besar di Indonesia seperti Tokopedia, Shopee, Blibli, Lazada, Bukalapak, Orami, JD.ID, MatahariMall, dan Zalora. Melalui pengumpulan data pasar, fitur aplikasi, strategi marketing, jumlah pengguna, serta performa penjualan, proyek ini bertujuan memahami kekuatan dan kelemahan masing-masing platform.	2025	assets/images/projects/1765085912_281.jpg	\N	\N	published	2025-12-07 12:38:32.314534+07	2025-12-07 12:38:32.314534+07	11	\N	\N
11	Laporan Kelengkapan Data Covid-19 di Provinsi Indonesia	laporan-kelengkapan-data-covid-19-di-provinsi-indonesia	Grafik nasional merangkum skor kelengkapan data Covid-19 di situs resmi provinsi, menunjukkan ketimpangan kualitas pelaporan data antar daerah.	Laporan ini memetakan tingkat kelengkapan data Covid-19 dari berbagai situs pemerintah provinsi di Indonesia. Grafik menunjukkan bahwa sebagian besar provinsi berada pada skor 2 dan 3, sementara hanya satu provinsi memiliki skor paling rendah (1). Selain itu, terdapat enam provinsi dengan status tidak diketahui akibat keterbatasan akses informasi. Data ini digunakan sebagai dasar evaluasi transparansi informasi publik, efektivitas pelaporan kesehatan, dan penyusunan kebijakan nasional yang lebih akurat.	2025	assets/images/projects/1765086807_868.jpg	\N	\N	published	2025-12-07 12:53:27.971246+07	2025-12-07 12:53:27.971246+07	10	\N	\N
12	Teknologi AR Meningkatkan Pengalaman Belanja Furnitur	teknologi-ar-meningkatkan-pengalaman-belanja-furnitur	Aplikasi belanja furnitur kini memungkinkan pengguna melihat produk secara virtual di dalam ruangan mereka menggunakan teknologi AR.	Platform belanja furnitur modern menghadirkan fitur Augmented Reality yang memungkinkan pengguna mencoba kursi, sofa, atau perabot lain langsung di ruangan mereka menggunakan kamera ponsel. Pengguna dapat memilih warna, bahan, hingga ukuran sebelum melihat obyek 3D muncul secara realistis. Teknologi ini mengurangi risiko kesalahan pembelian sekaligus meningkatkan pengalaman belanja digital. Aplikasi ini menjadi salah satu terobosan penting dalam industri furnitur berbasis e-commerce.	2025	assets/images/projects/1765086951_331.jpg	\N	\N	published	2025-12-07 12:55:51.035215+07	2025-12-07 12:55:51.035215+07	1	\N	\N
13	Kampus Luncurkan Sistem Akademik Digital Terintegrasi	kampus-luncurkan-sistem-akademik-digital-terintegrasi	Perguruan tinggi menghadirkan SIAKAD versi baru dengan tampilan modern dan fitur yang lebih terintegrasi untuk mahasiswa dan dosen	Sebuah perguruan tinggi resmi merilis Sistem Informasi Akademik (SIAKAD) terbaru yang mengusung desain modern, navigasi lebih rapi, dan keamanan data yang lebih kuat. Melalui platform ini, mahasiswa dapat mengakses KRS, KHS, riwayat mata kuliah, jadwal perkuliahan, serta informasi akademik lainnya dalam satu dashboard. Sistem ini juga mempermudah dosen dalam mengelola nilai, absensi, dan informasi kelas. Pembaruan ini menjadi bagian dari transformasi digital universitas.	2024	assets/images/projects/1765087024_991.jpg	\N	\N	published	2025-12-07 12:57:04.100403+07	2025-12-07 12:57:04.100403+07	5	\N	\N
14	Proyek Game Edukasi: “Nusantara Quest: Belajar Sejarah Indonesia”	proyek-game-edukasi-nusantara-quest-belajar-sejarah-indonesia	“Nusantara Quest” adalah sebuah game edukasi berbasis mobile dan desktop yang mengajak pemain menjelajahi sejarah Indonesia melalui misi, kuis, mini-game, dan petualangan interaktif. Game ini dirancang untuk siswa, pelajar, dan masyarakat umum agar lebih memahami peristiwa penting, tokoh pahlawan, budaya, geografi, dan warisan nasional Indonesia dengan cara seru dan mudah.	Proyek ini bertujuan menciptakan sebuah game edukasi yang menggabungkan story-based adventure, kuis, dan simulasi sejarah untuk memperkenalkan sejarah Indonesia secara interaktif. Pemain akan berperan sebagai “Penjaga Arsip Nusantara”—seorang penjelajah digital yang bertugas memulihkan ingatan sejarah Indonesia yang hilang akibat gangguan dalam sistem arsip virtual.\r\n\r\nDalam game ini, pemain akan memasuki era-era sejarah Indonesia, mulai dari kerajaan kuno hingga era kemerdekaan. Setiap era memiliki dunia dan misi tersendiri	2024	assets/images/projects/1765087178_442.jpg	\N	https://demo.labpolinema.ac.id/game-edukasi	published	2025-12-07 12:59:38.302225+07	2025-12-07 13:00:32.506479+07	7	\N	\N
15	Proyek IoT: “Smart Flood Monitoring System – Sistem Pemantau Banjir Berbasis IoT”	proyek-iot-smart-flood-monitoring-system-sistem-pemantau-banjir-berbasis-iot	“Smart Flood Monitoring System” adalah proyek IoT yang menggunakan sensor ultrasonik untuk memantau ketinggian air sungai atau selokan secara real-time. Data dikirim ke aplikasi untuk memberikan peringatan dini banjir kepada masyarakat.	Proyek ini mengembangkan sistem pemantauan banjir berbasis IoT yang bekerja secara otomatis 24 jam. Sensor ultrasonik dipasang di atas permukaan sungai/selokan untuk mengukur perubahan ketinggian air. Data dikirimkan melalui internet ke dashboard monitoring. Ketika air mulai naik melewati batas aman, sistem mengirimkan notifikasi ke warga atau petugas BPBD.\r\n\r\nFitur Utama:\r\n1. Monitoring Ketinggian Air Real-Time\r\n\r\nSensor ultrasonik membaca jarak air ke sensor secara berkala. Data langsung tampil dalam bentuk grafik.\r\n\r\n2. Peringatan Dini (Early Warning)\r\n\r\nJika air mencapai level berbahaya, sistem akan mengirimkan notifikasi ke smartphone atau sirene otomatis.\r\n\r\n3. Dashboard Online\r\n\r\nMenampilkan:\r\n\r\nLevel air\r\n\r\nStatus aman/siaga/bahaya\r\n\r\nGrafik kenaikan air per jam\r\n\r\nRiwayat data\r\n\r\n4. Sistem Hemat Energi\r\n\r\nMenggunakan power-saving mode, cocok untuk dipasang di lapangan.\r\n\r\n5. Komunikasi Jarak Jauh\r\n\r\nBisa menggunakan WiFi, LoRa, atau GSM (SIM800L) untuk wilayah tanpa WiFi.	2023	assets/images/projects/1765088344_265.webp	\N	\N	published	2025-12-07 13:19:04.642387+07	2025-12-07 13:19:04.642387+07	4	\N	\N
\.


--
-- TOC entry 3549 (class 0 OID 99482)
-- Dependencies: 238
-- Data for Name: tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tags (id, name, slug) FROM stdin;
1	Android	android
2	iOS	ios
3	Flutter	flutter
4	Augmented Reality	augmented-reality
5	WebGL	webgl
6	Machine Learning	machine-learning
7	Deep Learning	deep-learning
8	Arduino	arduino
9	Figma	figma
10	React	react
\.


--
-- TOC entry 3551 (class 0 OID 99486)
-- Dependencies: 240
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, password, role, created_at, updated_at) FROM stdin;
2	Editor 1	editor1@labpolinema.ac.id	$2y$10$abc1234567890examplehashhashhash	editor	2025-11-10 19:13:54.974842+07	2025-11-10 19:13:54.974842+07
3	adam	adamkian09@gmail.com	1234	anggota	2025-11-17 20:57:16.471984+07	2025-11-17 20:57:16.471984+07
5	adam	adamkull36@gmail.com	1234	anggota	2025-11-17 21:16:53.032775+07	2025-11-17 21:16:53.032775+07
1	Admin Lab	admin@labpolinema.ac.id	admin123	admin	2025-11-10 19:13:54.974842+07	2025-11-10 19:13:54.974842+07
6	admin2	admin2@dummy.com	12345	admin	2025-11-22 20:52:31.324255+07	2025-11-22 20:52:31.324255+07
7	sandy	sandy@gmail.com	admin	admin	2025-11-25 17:24:05.003777+07	2025-11-25 17:24:05.003777+07
8	serigala	serigala@gmail.com	12345	anggota	2025-12-01 11:03:53.351061+07	2025-12-01 11:03:53.351061+07
\.


--
-- TOC entry 3575 (class 0 OID 0)
-- Dependencies: 215
-- Name: activity_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.activity_logs_id_seq', 2, true);


--
-- TOC entry 3576 (class 0 OID 0)
-- Dependencies: 217
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 12, true);


--
-- TOC entry 3577 (class 0 OID 0)
-- Dependencies: 219
-- Name: comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.comments_id_seq', 9, true);


--
-- TOC entry 3578 (class 0 OID 0)
-- Dependencies: 221
-- Name: feedback_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.feedback_id_seq', 78, true);


--
-- TOC entry 3579 (class 0 OID 0)
-- Dependencies: 223
-- Name: lab_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lab_profile_id_seq', 2, true);


--
-- TOC entry 3580 (class 0 OID 0)
-- Dependencies: 225
-- Name: media_assets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.media_assets_id_seq', 16, true);


--
-- TOC entry 3581 (class 0 OID 0)
-- Dependencies: 227
-- Name: members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.members_id_seq', 4, true);


--
-- TOC entry 3582 (class 0 OID 0)
-- Dependencies: 230
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.news_id_seq', 12, true);


--
-- TOC entry 3583 (class 0 OID 0)
-- Dependencies: 232
-- Name: news_tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.news_tags_id_seq', 1, false);


--
-- TOC entry 3584 (class 0 OID 0)
-- Dependencies: 234
-- Name: project_members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_members_id_seq', 2, true);


--
-- TOC entry 3585 (class 0 OID 0)
-- Dependencies: 237
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.projects_id_seq', 15, true);


--
-- TOC entry 3586 (class 0 OID 0)
-- Dependencies: 239
-- Name: tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tags_id_seq', 10, true);


--
-- TOC entry 3587 (class 0 OID 0)
-- Dependencies: 241
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 7, true);


--
-- TOC entry 3312 (class 2606 OID 99509)
-- Name: activity_logs activity_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_pkey PRIMARY KEY (id);


--
-- TOC entry 3314 (class 2606 OID 99511)
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- TOC entry 3316 (class 2606 OID 99513)
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- TOC entry 3318 (class 2606 OID 99515)
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- TOC entry 3320 (class 2606 OID 99517)
-- Name: feedback feedback_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.feedback
    ADD CONSTRAINT feedback_pkey PRIMARY KEY (id);


--
-- TOC entry 3322 (class 2606 OID 99519)
-- Name: lab_profile lab_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile
    ADD CONSTRAINT lab_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 3324 (class 2606 OID 99521)
-- Name: media_assets media_assets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.media_assets
    ADD CONSTRAINT media_assets_pkey PRIMARY KEY (id);


--
-- TOC entry 3326 (class 2606 OID 99523)
-- Name: members members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.members
    ADD CONSTRAINT members_pkey PRIMARY KEY (id);


--
-- TOC entry 3332 (class 2606 OID 99525)
-- Name: news news_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- TOC entry 3334 (class 2606 OID 99527)
-- Name: news news_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_slug_key UNIQUE (slug);


--
-- TOC entry 3337 (class 2606 OID 99529)
-- Name: news_tags news_tags_news_id_tag_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT news_tags_news_id_tag_id_key UNIQUE (news_id, tag_id);


--
-- TOC entry 3339 (class 2606 OID 99531)
-- Name: news_tags news_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT news_tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3341 (class 2606 OID 99533)
-- Name: project_members project_members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_pkey PRIMARY KEY (id);


--
-- TOC entry 3343 (class 2606 OID 99535)
-- Name: project_tags project_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT project_tags_pkey PRIMARY KEY (project_id, tag_id);


--
-- TOC entry 3346 (class 2606 OID 99537)
-- Name: projects projects_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 3348 (class 2606 OID 99539)
-- Name: projects projects_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_slug_key UNIQUE (slug);


--
-- TOC entry 3350 (class 2606 OID 99541)
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3352 (class 2606 OID 99543)
-- Name: tags tags_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_slug_key UNIQUE (slug);


--
-- TOC entry 3354 (class 2606 OID 99545)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3356 (class 2606 OID 99547)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3357 (class 1259 OID 99618)
-- Name: idx_mv_dashboard_refresh; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_dashboard_refresh ON public.mv_lab_dashboard_stats USING btree (total_news);


--
-- TOC entry 3358 (class 1259 OID 99624)
-- Name: idx_mv_monthly_activity_month; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_monthly_activity_month ON public.mv_monthly_activity USING btree (month DESC);


--
-- TOC entry 3362 (class 1259 OID 99643)
-- Name: idx_mv_news_stats_category; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_news_stats_category ON public.mv_news_with_stats USING btree (category);


--
-- TOC entry 3363 (class 1259 OID 99642)
-- Name: idx_mv_news_stats_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_news_stats_created ON public.mv_news_with_stats USING btree (created_at DESC);


--
-- TOC entry 3359 (class 1259 OID 99632)
-- Name: idx_mv_project_details_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_project_details_id ON public.mv_project_details USING btree (id);


--
-- TOC entry 3360 (class 1259 OID 99634)
-- Name: idx_mv_project_details_rating; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_project_details_rating ON public.mv_project_details USING btree (avg_rating DESC);


--
-- TOC entry 3361 (class 1259 OID 99633)
-- Name: idx_mv_project_details_year; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_project_details_year ON public.mv_project_details USING btree (year DESC);


--
-- TOC entry 3330 (class 1259 OID 99548)
-- Name: idx_news_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_news_created_at ON public.news USING btree (created_at);


--
-- TOC entry 3335 (class 1259 OID 99549)
-- Name: idx_news_tags_tag; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_news_tags_tag ON public.news_tags USING btree (tag_id);


--
-- TOC entry 3344 (class 1259 OID 99550)
-- Name: idx_projects_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_projects_created_at ON public.projects USING btree (created_at);


--
-- TOC entry 3327 (class 1259 OID 99551)
-- Name: mv_feedback_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX mv_feedback_created ON public.mv_feedback_summary USING btree (created_epoch);


--
-- TOC entry 3328 (class 1259 OID 99552)
-- Name: mv_feedback_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX mv_feedback_id ON public.mv_feedback_summary USING btree (id);


--
-- TOC entry 3329 (class 1259 OID 99553)
-- Name: mv_feedback_read; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX mv_feedback_read ON public.mv_feedback_summary USING btree (is_read);


--
-- TOC entry 3374 (class 2620 OID 99554)
-- Name: comments update_comments_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_comments_updated_at BEFORE UPDATE ON public.comments FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3375 (class 2620 OID 99651)
-- Name: news update_news_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_news_updated_at BEFORE UPDATE ON public.news FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3376 (class 2620 OID 99650)
-- Name: projects update_projects_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_projects_updated_at BEFORE UPDATE ON public.projects FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3377 (class 2620 OID 99652)
-- Name: users update_users_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3364 (class 2606 OID 99555)
-- Name: comments fk_comments_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3366 (class 2606 OID 99560)
-- Name: news_tags fk_news_tags_news; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT fk_news_tags_news FOREIGN KEY (news_id) REFERENCES public.news(id) ON DELETE CASCADE;


--
-- TOC entry 3367 (class 2606 OID 99565)
-- Name: news_tags fk_news_tags_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT fk_news_tags_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3365 (class 2606 OID 99570)
-- Name: news fk_news_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT fk_news_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3372 (class 2606 OID 99575)
-- Name: projects fk_projects_category; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_category FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- TOC entry 3370 (class 2606 OID 99580)
-- Name: project_tags fk_projects_tags_project; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_projects_tags_project FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3371 (class 2606 OID 99585)
-- Name: project_tags fk_projects_tags_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_projects_tags_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3373 (class 2606 OID 99590)
-- Name: projects fk_projects_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3368 (class 2606 OID 99595)
-- Name: project_members project_members_member_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_member_id_fkey FOREIGN KEY (member_id) REFERENCES public.members(id) ON DELETE CASCADE;


--
-- TOC entry 3369 (class 2606 OID 99600)
-- Name: project_members project_members_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_project_id_fkey FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3539 (class 0 OID 99444)
-- Dependencies: 228 3558
-- Name: mv_feedback_summary; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_feedback_summary;


--
-- TOC entry 3553 (class 0 OID 99613)
-- Dependencies: 242 3558
-- Name: mv_lab_dashboard_stats; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_lab_dashboard_stats;


--
-- TOC entry 3554 (class 0 OID 99620)
-- Dependencies: 243 3558
-- Name: mv_monthly_activity; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_monthly_activity;


--
-- TOC entry 3556 (class 0 OID 99635)
-- Dependencies: 245 3558
-- Name: mv_news_with_stats; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_news_with_stats;


--
-- TOC entry 3555 (class 0 OID 99625)
-- Dependencies: 244 3558
-- Name: mv_project_details; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_project_details;


-- Completed on 2025-12-08 19:07:54

--
-- PostgreSQL database dump complete
--

\unrestrict 2cDGLkgwc7x9kpfXb3dH0QEqzXi5EFH4zuybUopg2v0a6HxPg7rMorZjG5CecPh

