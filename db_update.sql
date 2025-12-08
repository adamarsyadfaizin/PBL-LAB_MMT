--
-- PostgreSQL database dump
--

\restrict e2L8ukLQX7WlSzki1bjDY2hRKav1CqUobaOmbltn0F2eNhHhR5EtDq7TY2cD09V

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

-- Started on 2025-12-08 14:18:11

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
-- TOC entry 257 (class 1255 OID 99334)
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
-- TOC entry 259 (class 1255 OID 99336)
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
-- TOC entry 260 (class 1255 OID 99337)
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
-- TOC entry 258 (class 1255 OID 99335)
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
-- TOC entry 245 (class 1255 OID 99341)
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
-- TOC entry 243 (class 1255 OID 99302)
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
-- TOC entry 244 (class 1255 OID 99333)
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
-- TOC entry 261 (class 1255 OID 99098)
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
-- TOC entry 214 (class 1259 OID 99099)
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 99102)
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
-- TOC entry 3541 (class 0 OID 0)
-- Dependencies: 215
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- TOC entry 216 (class 1259 OID 99103)
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
-- TOC entry 217 (class 1259 OID 99112)
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
-- TOC entry 3542 (class 0 OID 0)
-- Dependencies: 217
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.comments_id_seq OWNED BY public.comments.id;


--
-- TOC entry 218 (class 1259 OID 99113)
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
-- TOC entry 219 (class 1259 OID 99120)
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
-- TOC entry 3543 (class 0 OID 0)
-- Dependencies: 219
-- Name: feedback_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.feedback_id_seq OWNED BY public.feedback.id;


--
-- TOC entry 220 (class 1259 OID 99121)
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
    contact_title character varying(255) DEFAULT 'Kontak Kami'::character varying
);


ALTER TABLE public.lab_profile OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 99137)
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
-- TOC entry 3544 (class 0 OID 0)
-- Dependencies: 221
-- Name: lab_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lab_profile_id_seq OWNED BY public.lab_profile.id;


--
-- TOC entry 222 (class 1259 OID 99138)
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
    CONSTRAINT media_assets_rating_check CHECK (((rating IS NULL) OR ((rating >= 1) AND (rating <= 5))))
);


ALTER TABLE public.media_assets OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 99145)
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
-- TOC entry 3545 (class 0 OID 0)
-- Dependencies: 223
-- Name: media_assets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.media_assets_id_seq OWNED BY public.media_assets.id;


--
-- TOC entry 224 (class 1259 OID 99146)
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
-- TOC entry 225 (class 1259 OID 99151)
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
-- TOC entry 3546 (class 0 OID 0)
-- Dependencies: 225
-- Name: members_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.members_id_seq OWNED BY public.members.id;


--
-- TOC entry 226 (class 1259 OID 99152)
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
-- TOC entry 233 (class 1259 OID 99174)
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
-- TOC entry 237 (class 1259 OID 99188)
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
-- TOC entry 239 (class 1259 OID 99303)
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
-- TOC entry 240 (class 1259 OID 99309)
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
-- TOC entry 228 (class 1259 OID 99162)
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
-- TOC entry 242 (class 1259 OID 99324)
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
-- TOC entry 230 (class 1259 OID 99167)
-- Name: project_members; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_members (
    id integer NOT NULL,
    project_id integer NOT NULL,
    member_id integer NOT NULL
);


ALTER TABLE public.project_members OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 99314)
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
-- TOC entry 227 (class 1259 OID 99161)
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
-- TOC entry 3547 (class 0 OID 0)
-- Dependencies: 227
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.news_id_seq OWNED BY public.news.id;


--
-- TOC entry 229 (class 1259 OID 99166)
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
-- TOC entry 3548 (class 0 OID 0)
-- Dependencies: 229
-- Name: news_tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.news_tags_id_seq OWNED BY public.news_tags.id;


--
-- TOC entry 231 (class 1259 OID 99170)
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
-- TOC entry 3549 (class 0 OID 0)
-- Dependencies: 231
-- Name: project_members_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_members_id_seq OWNED BY public.project_members.id;


--
-- TOC entry 232 (class 1259 OID 99171)
-- Name: project_tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_tags (
    project_id bigint NOT NULL,
    tag_id bigint NOT NULL
);


ALTER TABLE public.project_tags OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 99183)
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
-- TOC entry 3550 (class 0 OID 0)
-- Dependencies: 234
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.projects_id_seq OWNED BY public.projects.id;


--
-- TOC entry 235 (class 1259 OID 99184)
-- Name: tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tags (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.tags OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 99187)
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
-- TOC entry 3551 (class 0 OID 0)
-- Dependencies: 236
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tags_id_seq OWNED BY public.tags.id;


--
-- TOC entry 238 (class 1259 OID 99196)
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
-- TOC entry 3552 (class 0 OID 0)
-- Dependencies: 238
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3256 (class 2604 OID 99197)
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- TOC entry 3257 (class 2604 OID 99198)
-- Name: comments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments ALTER COLUMN id SET DEFAULT nextval('public.comments_id_seq'::regclass);


--
-- TOC entry 3261 (class 2604 OID 99199)
-- Name: feedback id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.feedback ALTER COLUMN id SET DEFAULT nextval('public.feedback_id_seq'::regclass);


--
-- TOC entry 3264 (class 2604 OID 99200)
-- Name: lab_profile id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile ALTER COLUMN id SET DEFAULT nextval('public.lab_profile_id_seq'::regclass);


--
-- TOC entry 3276 (class 2604 OID 99201)
-- Name: media_assets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.media_assets ALTER COLUMN id SET DEFAULT nextval('public.media_assets_id_seq'::regclass);


--
-- TOC entry 3278 (class 2604 OID 99202)
-- Name: members id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.members ALTER COLUMN id SET DEFAULT nextval('public.members_id_seq'::regclass);


--
-- TOC entry 3279 (class 2604 OID 99203)
-- Name: news id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news ALTER COLUMN id SET DEFAULT nextval('public.news_id_seq'::regclass);


--
-- TOC entry 3284 (class 2604 OID 99204)
-- Name: news_tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags ALTER COLUMN id SET DEFAULT nextval('public.news_tags_id_seq'::regclass);


--
-- TOC entry 3286 (class 2604 OID 99205)
-- Name: project_members id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members ALTER COLUMN id SET DEFAULT nextval('public.project_members_id_seq'::regclass);


--
-- TOC entry 3287 (class 2604 OID 99206)
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects ALTER COLUMN id SET DEFAULT nextval('public.projects_id_seq'::regclass);


--
-- TOC entry 3291 (class 2604 OID 99207)
-- Name: tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags ALTER COLUMN id SET DEFAULT nextval('public.tags_id_seq'::regclass);


--
-- TOC entry 3292 (class 2604 OID 99208)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3507 (class 0 OID 99099)
-- Dependencies: 214
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
-- TOC entry 3509 (class 0 OID 99103)
-- Dependencies: 216
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
-- TOC entry 3511 (class 0 OID 99113)
-- Dependencies: 218
-- Data for Name: feedback; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.feedback (id, nama_lengkap, email, subjek, pesan, created_at, is_read) FROM stdin;
1	Test Name	test@email.com	Test Subject	Test Message	2025-11-18 11:40:22.286125	f
2	TEST_AUTO	test@auto.com	Test Auto	Pesan test otomatis	2025-11-18 12:43:01.132644	f
3	DEBUG TEST 2025-11-18 06:15:46	debug@test.com	Test Subject	Test Message from Debug	2025-11-18 13:15:46.351984	f
4	Test from Connection Test	conn_test@test.com	Test	Message	2025-11-18 13:17:06.249653	f
5	Test from db.php	db_test@test.com	Test	Message	2025-11-18 13:17:06.254501	f
6	Adam	adamkull36@gmail.com	Halooo	dihwhqduihwduiwuifuifguie	2025-11-18 13:27:50.811762	f
7	Adam	adamkull36@gmail.com	Halooo	dihwhqduihwduiwuifuifguie	2025-11-18 13:32:06.779634	f
8	Adam	adamkull36@gmail.com	Halooo	dihwhqduihwduiwuifuifguie	2025-11-18 13:34:50.567231	f
9	gggueguerrfguerf	wknervrvbvbvjvbe@mial.com	wnferjferjfbrejfbrejfbrf	wefibrbrbfjrefbjrebregggre	2025-11-18 13:37:02.92979	f
10	gggueguerrfguerf	wknervrvbvbvjvbe@mial.com	wnferjferjfbrejfbrejfbrf	wefibrbrbfjrefbjrebregggre	2025-11-18 13:37:31.033334	f
11	dwdhvdsdfeaeasvv	filhiohregiorehgiethuithere@mail.com	reitherseitiheruthreuthrueheret	etieryuwyrewuiyrwiwuruyuwryuewyriery	2025-11-18 13:37:58.479978	f
12	dwdhvdsdfeaeasvv	filhiohregiorehgiethuithere@mail.com	reitherseitiheruthreuthrueheret	etieryuwyrewuiyrwiwuruyuwryuewyriery	2025-11-18 13:40:00.969305	f
13	ugfurgerutgueritr	dyqgyuewewdfy@gmail.com	uiwbeuwyfewyufvewuyf	fidhfiewhfiewhfiwhfiehifhi	2025-11-18 13:40:15.683503	f
14	ugfurgerutgueritr	dyqgyuewewdfy@gmail.com	uiwbeuwyfewyufvewuyf	fidhfiewhfiewhfiwhfiehifhi	2025-11-18 13:41:30.629052	f
15	duewewfgewf	dhgasd@gmail.com	wqujdhwqudhqwuhewuf	qwdfuidgqewgewuigewugewfgewuigewuige	2025-11-18 13:41:44.760237	f
16	Adam	adamkill36@gmail.com	reitherseitiheruthreuthrueheret	qdbeqwifbeqwufyewvfqewqfuufawfewbfuewbyubew	2025-11-18 13:48:41.771203	f
17	duewewfgewf	dhgasd@gmail.com	wqujdhwqudhqwuhewuf	qwdfuidgqewgewuigewugewfgewuigewuige	2025-11-18 13:49:53.423314	f
18	Gigan	Giganjdbdb@gmail.com	dubewudbeudbeufbew	euivneuivbruivbuivbeierivberui	2025-11-18 13:50:10.212332	f
19	Gigan	Giganjdbdb@gmail.com	dubewudbeudbeufbew	euivneuivbruivbuivbeierivberui	2025-11-18 13:50:39.326731	f
20	Gigan	Giganjdbdb@gmail.com	dubewudbeudbeufbew	euivneuivbruivbuivbeierivberui	2025-11-18 13:50:48.155282	f
23	Hoooo	Hooo@gmail.com	reitherseitiheruthreuthrueheret	qlibqwidbuiqawsdcvsfivuf	2025-11-18 13:52:56.108803	f
24	Hoooo	Hooo@gmail.com	reitherseitiheruthreuthrueheret	qlibqwidbuiqawsdcvsfivuf	2025-11-18 13:53:43.064005	f
25	MA Q	adamkill36@gmail.com	reitherseitiheruthreuthrueheret	efiohewfhw9fhwfhwehfiohwefoiwehf	2025-11-18 13:53:53.351154	f
26	MA Q	adamkill36@gmail.com	reitherseitiheruthreuthrueheret	efiohewfhw9fhwfhwehfiohwefoiwehf	2025-11-18 13:59:29.630958	f
27	MA QQ	adamkill36@gmail.com	Halooo	dwfiwehfuiewfguewifvuiewgfewgfuewguifgqewuf	2025-11-18 14:00:05.062716	f
28	MA QQ	adamkill36@gmail.com	Halooo	dwfiwehfuiewfguewifvuiewgfewgfuewguifgqewuf	2025-11-18 14:00:41.391158	f
29	MA QQ	adamkill36@gmail.com	Halooo	dwfiwehfuiewfguewifvuiewgfewgfuewguifgqewuf	2025-11-18 14:02:34.487951	f
30	MA Qjjj	adamkull36@gmail.com	Halooo	kbdb32uirvwfuie3vfi2vfq13wv	2025-11-18 14:02:47.264955	f
31	MA Qjjj	adamkull36@gmail.com	Halooo	kbdb32uirvwfuie3vfi2vfq13wv	2025-11-18 14:05:25.026367	f
32	PUAN	adamkull36@gmail.com	Halooo	kbdb32uirvwfuie3vfi2vfq13wv	2025-11-18 14:05:34.966623	f
33	PUAN	adamkull36@gmail.com	Halooo	kbdb32uirvwfuie3vfi2vfq13wv	2025-11-18 14:05:41.036779	f
34	PUAN	adamkull36@gmail.com	Halooo	kbdb32uirvwfuie3vfi2vfq13wv	2025-11-18 14:10:12.969714	f
35	PUAN	adamkull36@gmail.com	Halooo	kbdb32uirvwfuie3vfi2vfq13wv	2025-11-18 14:12:32.790082	f
36	PUAN	adamkull36@gmail.com	Halooo	kbdb32uirvwfuie3vfi2vfq13wv	2025-11-18 14:12:48.044332	f
37	PUAN	adamkull36@gmail.com	Halooo	kbdb32uirvwfuie3vfi2vfq13wv	2025-11-18 14:13:57.143274	f
47	MA Qklkoko	jqbdjb@gmail.com	reitherseitiheruthreuthrueheretduigdyu	qwdhvwqhjdvehjdvewhjwdvfhjewvfhjewvfhjhewvfew	2025-11-18 14:30:32.230705	f
48	MA Qklkoko	jqbdjb@gmail.com	reitherseitiheruthreuthrueheretduigdyu	qwdhvwqhjdvehjdvewhjwdvfhjewvfhjewvfhjhewvfew	2025-11-18 14:32:15.28086	f
49	MA Qhhh	adamkill36@gmail.com	wqujdhwqudhqwuhewuf	dbwqifbqwjifeWGVGVIOSGVOIEDWHFHWEIOHFE	2025-11-18 14:32:28.878134	f
50	MA Qhhh	adamkill36@gmail.com	wqujdhwqudhqwuhewuf	dbwqifbqwjifeWGVGVIOSGVOIEDWHFHWEIOHFE	2025-11-18 14:33:34.087873	f
52	MA Q))))	IJIIJI@gmail.com	sfgndrnrininirfhb	ewdecwtyufbiodrzngodfnbiodfngiodfngiodd	2025-11-18 14:41:14.517868	f
53	MA Q))))	IJIIJI@gmail.com	sfgndrnrininirfhb	ewdecwtyufbiodrzngodfnbiodfngiodfngiodd	2025-11-18 14:44:33.4514	f
54	MA Qhbhbhb	manuelneuer@gmail.com	wfjbewjfbewfbjkewfbejkfewb	2ejkjbdhewjvfhejwfvewhjfvewhjvewhjfvewhjvhjvhjewvf	2025-11-18 14:45:01.214896	f
55	MA Qhbhbhb	manuelneuer@gmail.com	wfjbewjfbewfbjkewfbejkfewb	2ejkjbdhewjvfhejwfvewhjfvewhjvewhjfvewhjvhjvhjewvf	2025-11-18 14:46:25.075806	f
56	MANUEL NEUER	timothy@gmail.com	waudbwqjfbqehjfehjf	djdfvhejevfhjesvfhjevfhjewvfhjewvfjhevfjhewvhj	2025-11-18 14:46:47.960646	f
57	Adam	adamkill36@gmail.com	reitherseitiheruthreuthrueheret	qdbeqwifbeqwufyewvfqewqfuufawfewbfuewbyubew	2025-11-18 14:47:59.021584	f
58	Adam	adamkill36@gmail.com	reitherseitiheruthreuthrueheret	qdbeqwifbeqwufyewvfqewqfuufawfewbfuewbyubew	2025-11-18 14:49:57.879903	f
59	MANUEL NEUER	timothy@gmail.com	waudbwqjfbqehjfehjf	djdfvhejevfhjesvfhjevfhjewvfhjewvfjhevfjhewvhj	2025-11-18 14:50:10.5511	f
60	gsrngrjgnrjeg	GH2@gmail.com	aewjkfesjkbjke	wqiuqhuihruiqewqhewuifndsinvjidsnvdsjnvjdxnvjdsv	2025-11-18 14:50:30.894103	f
61	gsrngrjgnrjeg	GH2@gmail.com	aewjkfesjkbjke	wqiuqhuihruiqewqhewuifndsinvjidsnvdsjnvjdxnvjdsv	2025-11-18 14:56:18.179657	f
62	gsrngrjgnrjeg	GH2@gmail.com	aewjkfesjkbjke	wqiuqhuihruiqewqhewuifndsinvjidsnvdsjnvjdxnvjdsv	2025-11-18 15:00:00.497371	f
63	gsrngrjgnrjeg	GH2@gmail.com	aewjkfesjkbjke	wqiuqhuihruiqewqhewuifndsinvjidsnvdsjnvjdxnvjdsv	2025-11-18 15:01:14.083837	f
64	MA Qnjnjn	ejbfjke@gmail.com	Crypto	asdfghjwkwsmnfmvcshjdvjhdsvdshk	2025-11-18 15:01:33.684397	f
65	MA Qnjnjn	ejbfjke@gmail.com	Crypto	asdfghjwkwsmnfmvcshjdvjhdsvdshk	2025-11-18 15:03:54.046489	f
66	MA Qnjnjn	ejbfjke@gmail.com	Crypto	asdfghjwkwsmnfmvcshjdvjhdsvdshk	2025-11-18 15:06:08.491533	f
67	Adam	adamkill36@gmail.com	reitherseitiheruthreuthrueheret	qdbeqwifbeqwufyewvfqewqfuufawfewbfuewbyubew	2025-11-18 15:07:55.726234	f
68	MA Qnjnjn	ejbfjke@gmail.com	Crypto	asdfghjwkwsmnfmvcshjdvjhdsvdshk	2025-11-18 15:08:05.095654	f
69	MA Qnjnjn	ejbfjke@gmail.com	Crypto	asdfghjwkwsmnfmvcshjdvjhdsvdshk	2025-11-18 15:09:44.352875	f
70	MA Qnjnjn	ejbfjke@gmail.com	Crypto	asdfghjwkwsmnfmvcshjdvjhdsvdshk	2025-11-18 16:06:33.724292	f
72	MA QjnjnWKWKWK	dhgasd@gmail.com	wqujdhwqudhqwuhewuf	qdbeqwuifewvfyufewuifvewuifewvbfo3bvuof3w	2025-11-18 18:02:59.821321	f
73	MA QjnjnWKWKWK	dhgasd@gmail.com	wqujdhwqudhqwuhewuf	qdbeqwuifewvfyufewuifvewuifewvbfo3bvuof3w	2025-11-18 18:09:43.100482	f
74	MA Qvhvh	RAWR@gmail.com	diuvqudvq3udvucvdyu21	sjkjbcehj cehj ew vuyvdg23idg2gdg2qd	2025-11-18 18:10:05.649432	f
75	raihan	adamkian09@gmail.com	tugas	testting	2025-11-26 09:01:48.016737	f
76	raihan	buje@gmail.com	test	test\r\n<script>\r\nfunction say(){\r\nalert('hello');\r\nsay();\r\n}\r\n\r\nsay();\r\n</script>	2025-11-26 09:03:09.996582	t
77	raihan akbar	adamkull36@gmail.com	test	haiiiiiiiiiiiiiiii	2025-12-01 14:16:31.518804	t
\.


--
-- TOC entry 3513 (class 0 OID 99121)
-- Dependencies: 220
-- Data for Name: lab_profile; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lab_profile (id, visi, misi, sejarah, updated_at, alamat_lab, email_lab, telepon_lab, lokasi_lab, fb_link, x_link, ig_link, yt_link, linkedin, logo_path, hero_image_path, hero_title, hero_description, about_hero_image, news_hero_image, project_hero_image, gallery_hero_image, contact_hero_image, about_title, news_title, project_title, gallery_title, contact_title) FROM stdin;
2	Menjadi laboratorium multimedia dan mobile technology terdepan dalam inovasi dan kreativitas digital	Mengembangkan solusi digital inovatif melalui penelitian, pengembangan, dan kolaborasi dengan industri	Laboratorium Multimedia dan Mobile Tech didirikan pada tahun 2010 sebagai pusat pengembangan teknologi digital di Politeknik Negeri Malang	2025-11-17 13:52:20.720474+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	Profil Laboratorium	Berita & Kegiatan	Katalog Proyek	Galeri Multimedia	Kontak Kami
1	Menjadi lab unggulan	Meningkatkan riset dan inovasi	Didirikan tahun 2020	2025-11-18 10:58:08.336706+07	Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141	multimedia@gmail.com	(0341) 404424	Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Malang	https://facebook.com/polinema.mobilelab	https://twitter.com/polinema_mobile	https://instagram.com/polinema.mobilelab	https://youtube.com/c/PolinemaMobileLab	https://linkedin.com/company/polinema-mobile-lab	assets/images/logo-placeholder.png	assets/images/hero.jpg	LABORATORIUM MOBILE AND MULTIMEDIA TECH	Pusat pengembangan karya inovatif di bidang UI/UX, Game, AR/VR.	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	Profil Laboratorium	Berita & Kegiatan	Katalog Proyek	Galeri Multimedia	Kontak Kami
\.


--
-- TOC entry 3515 (class 0 OID 99138)
-- Dependencies: 222
-- Data for Name: media_assets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.media_assets (id, type, url, caption, created_at, deskripsi, rating) FROM stdin;
1	image	../assets/images/gallery/1.jpg	Workshop Pengembangan Aplikasi Android	2025-11-10 19:14:16.769618+07	\N	\N
2	image	../assets/images/gallery/2.jpg	Kegiatan Pameran Multimedia 2025	2025-11-10 19:14:16.769618+07	\N	\N
4	foto	https://files.catbox.moe/vgd0xe.png	Workshop AI di Polinema	2025-11-12 10:00:00+07	\N	\N
5	foto	https://files.catbox.moe/vgd0xe.png	Workshop AI di Polinema	2025-11-12 10:00:00+07	Kegiatan workshop pengenalan teknologi Artificial Intelligence bersama mahasiswa jurusan Multimedia MMT POLINEMA. Acara ini bertujuan untuk memperkenalkan penerapan AI dalam dunia industri kreatif dan pendidikan.	\N
8	video	https://www.youtube.com/embed/abcdefghij	Video Dokumentasi Lomba Game Dev	2025-11-17 13:41:05.691975+07	Lomba Game 2025	\N
3	video	../assets/images/gallery/videos/1.mp4	Demo Sistem Mobile Smart Attendance	2025-11-10 19:14:16.769618+07	\N	\N
6	foto	../assets/images/gallery/3.jpg	Kegiatan Dies Natalis	2025-11-17 13:41:05.691975+07	Hari ulang tahun kampus	\N
7	foto	../assets/images/gallery/4.jpg	Wisuda Mahasiswa	2025-11-17 13:41:05.691975+07	Hari kelulusan mahasiswa	\N
\.


--
-- TOC entry 3517 (class 0 OID 99146)
-- Dependencies: 224
-- Data for Name: members; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.members (id, name, role, avatar_url, linkedin_url, tags, scholar_url, youtube, facebook, instagram) FROM stdin;
4	Dewi Lestari	Sekretaris	../assets/images/team/dewi.jpg	https://linkedin.com/in/dewilestari	Sekretaris	https://scholar.google.com/citations?user=delta999	https://youtube.com/@deltastream	https://facebook.com/delta.user	https://instagram.com/delta.snap
3	Fajar Pratama	Anggota	../assets/images/team/fajar.jpg	https://linkedin.com/in/fajarpratama	Anggota	https://scholar.google.com/citations?user=gamma789	https://youtube.com/@gammaworld	https://facebook.com/gamma.page	https://instagram.com/gamma.life
2	Nadia Putri	Wakil Ketua	../assets/images/team/nadia.jpg	https://linkedin.com/in/nadiaputri	Wakil	https://scholar.google.com/citations?user=beta456	https://youtube.com/@betachannel	https://facebook.com/beta.profile	https://instagram.com/beta.ig
1	Rizky Ananda	Ketua Laboratorium	../assets/images/team/rizky.jpg	https://linkedin.com/in/rizkyananda	Ketua	https://scholar.google.com/citations?user=alpha123	https://youtube.com/@useralpha	https://facebook.com/user.alpha	https://instagram.com/user.alpha
\.


--
-- TOC entry 3519 (class 0 OID 99152)
-- Dependencies: 226
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.news (id, title, slug, summary, content, cover_image, status, created_at, updated_at, user_id, type, category) FROM stdin;
4	Pelatihan Internet of Things (IoT) untuk Dosen dan Mahasiswa	pelatihan-iot	Pelatihan ini memperkenalkan konsep dasar dan penerapan IoT.	Kegiatan pelatihan ini melibatkan dosen dan mahasiswa untuk memahami penerapan IoT dalam sistem pintar, serta implementasi sensor dan mikrokontroler untuk proyek real-time.	../assets/images/news/pelatihan-iot.jpg	published	2025-11-10 19:28:25.7442+07	2025-11-10 19:28:25.7442+07	\N	news	berita
1	Workshop Flutter 2025	workshop-flutter-2025	Pelatihan pengembangan aplikasi Flutter untuk mahasiswa tingkat akhir.	Laboratorium Mobile & Multimedia Tech mengadakan workshop pengembangan aplikasi Flutter untuk membantu mahasiswa memahami dasar cross-platform mobile app development.	../assets/images/news/flutter-workshop.jpg	published	2025-11-10 19:15:15.466339+07	2025-11-10 19:15:15.466339+07	\N	news	berita
2	AI dan Multimedia: Kolaborasi Masa Depan	ai-dan-multimedia	Membahas potensi integrasi AI dalam teknologi multimedia modern.	Seminar AI dan Multimedia membahas penerapan machine learning dalam pengolahan gambar dan video untuk meningkatkan pengalaman pengguna.	../assets/images/news/ai-multimedia.jpg	published	2025-11-10 19:15:28.421072+07	2025-11-10 19:15:28.421072+07	\N	news	berita
3	Pameran Proyek Akhir Mahasiswa	pameran-proyek-akhir	Pameran tahunan karya mahasiswa di bidang mobile dan multimedia.	Acara ini menampilkan hasil riset dan proyek inovatif mahasiswa yang dikembangkan di Laboratorium Mobile and Multimedia Tech.	../assets/images/news/exhibition.jpg	published	2025-11-10 19:15:46.951029+07	2025-11-10 19:15:46.951029+07	\N	news	berita
5	Kuliah Tamu: Inovasi Teknologi AI di Dunia Industri	kuliah-tamu-ai	Kegiatan kuliah tamu membahas penerapan AI dalam dunia industri modern.	Departemen Sistem Informasi mengadakan kuliah tamu bertema “Inovasi Teknologi AI di Dunia Industri” yang menghadirkan pembicara dari startup teknologi ternama. Kegiatan ini bertujuan memperkenalkan mahasiswa pada perkembangan terbaru dalam teknologi kecerdasan buatan.	../assets/images/news/kuliah-tamu-ai.jpg	published	2025-11-10 19:28:40.780585+07	2025-11-10 19:28:40.780585+07	\N	news	kegiatan
6	Workshop UI/UX Design untuk Mahasiswa	workshop-uiux	Workshop ini mengajarkan dasar-dasar desain antarmuka dan pengalaman pengguna.	Mahasiswa belajar langsung konsep desain UI/UX dari praktisi industri. Workshop ini juga membahas pentingnya user-centered design dalam pengembangan aplikasi modern.	../assets/images/news/workshop-uiux.jpg	published	2025-11-10 19:28:57.473885+07	2025-11-10 19:28:57.473885+07	\N	news	kegiatan
7	Rapat Persiapan Kompetisi Gemastik 2025	rapat-persiapan-kompetisi-gemastik-2025	Rapat koordinasi internal menjelang kompetisi Gemastik tahun 2025.	Seluruh tim dari Laboratorium Mobile and Multimedia Tech melakukan rapat persiapan untuk mengikuti Gemastik 2025. Fokus utama adalah pengembangan aplikasi dengan teknologi terbaru dan manajemen tim yang solid.	../assets/images/news/gemastik-2025.jpg	published	2025-11-10 19:29:10.914884+07	2025-11-24 01:27:03.440257+07	\N	news	kegiatan
\.


--
-- TOC entry 3521 (class 0 OID 99162)
-- Dependencies: 228
-- Data for Name: news_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.news_tags (id, news_id, tag_id, created_at) FROM stdin;
\.


--
-- TOC entry 3523 (class 0 OID 99167)
-- Dependencies: 230
-- Data for Name: project_members; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_members (id, project_id, member_id) FROM stdin;
1	1	1
2	1	2
\.


--
-- TOC entry 3525 (class 0 OID 99171)
-- Dependencies: 232
-- Data for Name: project_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_tags (project_id, tag_id) FROM stdin;
\.


--
-- TOC entry 3526 (class 0 OID 99174)
-- Dependencies: 233
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.projects (id, title, slug, summary, description, year, cover_image, repo_url, demo_url, status, created_at, updated_at, category_id, user_id, rating) FROM stdin;
1	IoT Smart Home Controller	iot-smart-home	Sistem pengendali rumah pintar berbasis IoT.	Proyek ini memungkinkan pengguna mengontrol perangkat rumah tangga dari jarak jauh melalui aplikasi mobile dan integrasi sensor IoT.	2023	../assets/images/projects/smarthome.jpg	https://github.com/labpolinema/smarthome	https://demo.labpolinema.ac.id/smarthome	1	2025-11-10 19:18:16.366231+07	2025-11-10 19:18:16.366231+07	4	\N	\N
2	Augmented Reality Learning App	ar-learning-app	Aplikasi pembelajaran interaktif berbasis AR untuk anak sekolah.	Proyek ini menggabungkan teknologi Augmented Reality untuk menciptakan pengalaman belajar yang menyenangkan dan imersif.	2024	../assets/images/projects/ar-learning.jpg	https://github.com/labpolinema/ar-learning	https://demo.labpolinema.ac.id/ar-learning	1	2025-11-10 19:18:30.048209+07	2025-11-10 19:18:30.048209+07	2	\N	\N
4	Smart Attendance System	smart-attendance	Aplikasi presensi berbasis pengenalan wajah untuk perkuliahan.	Proyek ini menggunakan teknologi face recognition untuk mencatat kehadiran mahasiswa secara otomatis dan real-time. Sistem ini dikembangkan menggunakan Python dan database PostgreSQL.	2025	../assets/images/projects/attendance.jpg	https://github.com/labpolinema/smart-attendance	https://demo.labpolinema.ac.id/attendance	published	2025-11-10 19:31:01.213185+07	2025-11-10 19:31:01.213185+07	1	\N	\N
6	Virtual Reality Tourism Experience	vr-tourism	Aplikasi tur virtual untuk destinasi wisata Indonesia.	Aplikasi VR ini menghadirkan pengalaman menjelajahi tempat wisata Indonesia dengan teknologi virtual reality, dikembangkan menggunakan Unity dan Oculus SDK.	2024	../assets/images/projects/vr-tourism.jpg	https://github.com/labpolinema/vr-tourism	https://demo.labpolinema.ac.id/vr-tourism	published	2025-11-10 19:31:42.529039+07	2025-11-10 19:31:42.529039+07	4	\N	\N
3	Game Edukasi Bahasa Indonesia	game-edukasi-bahasa-indonesia	Game ini dirancang untuk membantu anak-anak belajar kosakata dan tata bahasa melalui permainan interaktif yang menyenangkan dan edukatif.	Game ini dirancang untuk membantu anak-anak belajar kosakata dan tata bahasa melalui permainan interaktif yang menyenangkan dan edukatif.	2025	../assets/images/projects/game-edukasi.jpg	https://github.com/labpolinema/game-edukasi	https://demo.labpolinema.ac.id/game-edukasi	published	2025-11-10 19:30:41.150439+07	2025-11-24 01:30:19.435286+07	2	\N	\N
\.


--
-- TOC entry 3528 (class 0 OID 99184)
-- Dependencies: 235
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
-- TOC entry 3530 (class 0 OID 99188)
-- Dependencies: 237
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
-- TOC entry 3553 (class 0 OID 0)
-- Dependencies: 215
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 12, true);


--
-- TOC entry 3554 (class 0 OID 0)
-- Dependencies: 217
-- Name: comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.comments_id_seq', 9, true);


--
-- TOC entry 3555 (class 0 OID 0)
-- Dependencies: 219
-- Name: feedback_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.feedback_id_seq', 77, true);


--
-- TOC entry 3556 (class 0 OID 0)
-- Dependencies: 221
-- Name: lab_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lab_profile_id_seq', 2, true);


--
-- TOC entry 3557 (class 0 OID 0)
-- Dependencies: 223
-- Name: media_assets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.media_assets_id_seq', 9, true);


--
-- TOC entry 3558 (class 0 OID 0)
-- Dependencies: 225
-- Name: members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.members_id_seq', 4, true);


--
-- TOC entry 3559 (class 0 OID 0)
-- Dependencies: 227
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.news_id_seq', 7, true);


--
-- TOC entry 3560 (class 0 OID 0)
-- Dependencies: 229
-- Name: news_tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.news_tags_id_seq', 1, false);


--
-- TOC entry 3561 (class 0 OID 0)
-- Dependencies: 231
-- Name: project_members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_members_id_seq', 2, true);


--
-- TOC entry 3562 (class 0 OID 0)
-- Dependencies: 234
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.projects_id_seq', 8, true);


--
-- TOC entry 3563 (class 0 OID 0)
-- Dependencies: 236
-- Name: tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tags_id_seq', 10, true);


--
-- TOC entry 3564 (class 0 OID 0)
-- Dependencies: 238
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 7, true);


--
-- TOC entry 3300 (class 2606 OID 99210)
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- TOC entry 3302 (class 2606 OID 99212)
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- TOC entry 3304 (class 2606 OID 99214)
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- TOC entry 3306 (class 2606 OID 99216)
-- Name: feedback feedback_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.feedback
    ADD CONSTRAINT feedback_pkey PRIMARY KEY (id);


--
-- TOC entry 3308 (class 2606 OID 99218)
-- Name: lab_profile lab_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile
    ADD CONSTRAINT lab_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 3310 (class 2606 OID 99220)
-- Name: media_assets media_assets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.media_assets
    ADD CONSTRAINT media_assets_pkey PRIMARY KEY (id);


--
-- TOC entry 3312 (class 2606 OID 99222)
-- Name: members members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.members
    ADD CONSTRAINT members_pkey PRIMARY KEY (id);


--
-- TOC entry 3315 (class 2606 OID 99224)
-- Name: news news_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- TOC entry 3317 (class 2606 OID 99226)
-- Name: news news_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_slug_key UNIQUE (slug);


--
-- TOC entry 3320 (class 2606 OID 99228)
-- Name: news_tags news_tags_news_id_tag_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT news_tags_news_id_tag_id_key UNIQUE (news_id, tag_id);


--
-- TOC entry 3322 (class 2606 OID 99230)
-- Name: news_tags news_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT news_tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3324 (class 2606 OID 99232)
-- Name: project_members project_members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_pkey PRIMARY KEY (id);


--
-- TOC entry 3326 (class 2606 OID 99234)
-- Name: project_tags project_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT project_tags_pkey PRIMARY KEY (project_id, tag_id);


--
-- TOC entry 3329 (class 2606 OID 99236)
-- Name: projects projects_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 3331 (class 2606 OID 99238)
-- Name: projects projects_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_slug_key UNIQUE (slug);


--
-- TOC entry 3333 (class 2606 OID 99240)
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3335 (class 2606 OID 99242)
-- Name: tags tags_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_slug_key UNIQUE (slug);


--
-- TOC entry 3337 (class 2606 OID 99244)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3339 (class 2606 OID 99246)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3340 (class 1259 OID 99308)
-- Name: idx_mv_dashboard_refresh; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_dashboard_refresh ON public.mv_lab_dashboard_stats USING btree (total_news);


--
-- TOC entry 3341 (class 1259 OID 99313)
-- Name: idx_mv_monthly_activity_month; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_monthly_activity_month ON public.mv_monthly_activity USING btree (month DESC);


--
-- TOC entry 3345 (class 1259 OID 99332)
-- Name: idx_mv_news_stats_category; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_news_stats_category ON public.mv_news_with_stats USING btree (category);


--
-- TOC entry 3346 (class 1259 OID 99331)
-- Name: idx_mv_news_stats_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_news_stats_created ON public.mv_news_with_stats USING btree (created_at DESC);


--
-- TOC entry 3342 (class 1259 OID 99321)
-- Name: idx_mv_project_details_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_project_details_id ON public.mv_project_details USING btree (id);


--
-- TOC entry 3343 (class 1259 OID 99323)
-- Name: idx_mv_project_details_rating; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_project_details_rating ON public.mv_project_details USING btree (avg_rating DESC);


--
-- TOC entry 3344 (class 1259 OID 99322)
-- Name: idx_mv_project_details_year; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_mv_project_details_year ON public.mv_project_details USING btree (year DESC);


--
-- TOC entry 3313 (class 1259 OID 99247)
-- Name: idx_news_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_news_created_at ON public.news USING btree (created_at);


--
-- TOC entry 3318 (class 1259 OID 99248)
-- Name: idx_news_tags_tag; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_news_tags_tag ON public.news_tags USING btree (tag_id);


--
-- TOC entry 3327 (class 1259 OID 99249)
-- Name: idx_projects_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_projects_created_at ON public.projects USING btree (created_at);


--
-- TOC entry 3357 (class 2620 OID 99250)
-- Name: comments update_comments_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_comments_updated_at BEFORE UPDATE ON public.comments FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3358 (class 2620 OID 99339)
-- Name: news update_news_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_news_updated_at BEFORE UPDATE ON public.news FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3359 (class 2620 OID 99338)
-- Name: projects update_projects_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_projects_updated_at BEFORE UPDATE ON public.projects FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3360 (class 2620 OID 99340)
-- Name: users update_users_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3347 (class 2606 OID 99251)
-- Name: comments fk_comments_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3349 (class 2606 OID 99256)
-- Name: news_tags fk_news_tags_news; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT fk_news_tags_news FOREIGN KEY (news_id) REFERENCES public.news(id) ON DELETE CASCADE;


--
-- TOC entry 3350 (class 2606 OID 99261)
-- Name: news_tags fk_news_tags_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT fk_news_tags_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3348 (class 2606 OID 99266)
-- Name: news fk_news_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT fk_news_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3355 (class 2606 OID 99271)
-- Name: projects fk_projects_category; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_category FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- TOC entry 3353 (class 2606 OID 99276)
-- Name: project_tags fk_projects_tags_project; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_projects_tags_project FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3354 (class 2606 OID 99281)
-- Name: project_tags fk_projects_tags_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_projects_tags_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3356 (class 2606 OID 99286)
-- Name: projects fk_projects_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3351 (class 2606 OID 99291)
-- Name: project_members project_members_member_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_member_id_fkey FOREIGN KEY (member_id) REFERENCES public.members(id) ON DELETE CASCADE;


--
-- TOC entry 3352 (class 2606 OID 99296)
-- Name: project_members project_members_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_project_id_fkey FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3532 (class 0 OID 99303)
-- Dependencies: 239 3537
-- Name: mv_lab_dashboard_stats; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_lab_dashboard_stats;


--
-- TOC entry 3533 (class 0 OID 99309)
-- Dependencies: 240 3537
-- Name: mv_monthly_activity; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_monthly_activity;


--
-- TOC entry 3535 (class 0 OID 99324)
-- Dependencies: 242 3537
-- Name: mv_news_with_stats; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_news_with_stats;


--
-- TOC entry 3534 (class 0 OID 99314)
-- Dependencies: 241 3537
-- Name: mv_project_details; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_project_details;


-- Completed on 2025-12-08 14:18:11

--
-- PostgreSQL database dump complete
--

\unrestrict e2L8ukLQX7WlSzki1bjDY2hRKav1CqUobaOmbltn0F2eNhHhR5EtDq7TY2cD09V

