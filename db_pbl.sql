--
-- PostgreSQL database dump
--

\restrict LPcBdaJEsvt6xWYoZaHLVIanKkVadGOPMuiuaSQJEXyrWJJSHsCSdKYhCUR8UYx

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

-- Started on 2025-12-08 07:56:04

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
-- TOC entry 242 (class 1255 OID 26183)
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
-- TOC entry 241 (class 1259 OID 26600)
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
-- TOC entry 240 (class 1259 OID 26599)
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
-- TOC entry 3521 (class 0 OID 0)
-- Dependencies: 240
-- Name: activity_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.activity_logs_id_seq OWNED BY public.activity_logs.id;


--
-- TOC entry 214 (class 1259 OID 26184)
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 26187)
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
-- TOC entry 3522 (class 0 OID 0)
-- Dependencies: 215
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- TOC entry 216 (class 1259 OID 26188)
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
-- TOC entry 217 (class 1259 OID 26197)
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
-- TOC entry 3523 (class 0 OID 0)
-- Dependencies: 217
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.comments_id_seq OWNED BY public.comments.id;


--
-- TOC entry 218 (class 1259 OID 26198)
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
-- TOC entry 219 (class 1259 OID 26205)
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
-- TOC entry 3524 (class 0 OID 0)
-- Dependencies: 219
-- Name: feedback_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.feedback_id_seq OWNED BY public.feedback.id;


--
-- TOC entry 220 (class 1259 OID 26206)
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
-- TOC entry 221 (class 1259 OID 26222)
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
-- TOC entry 3525 (class 0 OID 0)
-- Dependencies: 221
-- Name: lab_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lab_profile_id_seq OWNED BY public.lab_profile.id;


--
-- TOC entry 222 (class 1259 OID 26223)
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
-- TOC entry 223 (class 1259 OID 26230)
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
-- TOC entry 3526 (class 0 OID 0)
-- Dependencies: 223
-- Name: media_assets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.media_assets_id_seq OWNED BY public.media_assets.id;


--
-- TOC entry 224 (class 1259 OID 26231)
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
-- TOC entry 225 (class 1259 OID 26236)
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
-- TOC entry 3527 (class 0 OID 0)
-- Dependencies: 225
-- Name: members_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.members_id_seq OWNED BY public.members.id;


--
-- TOC entry 239 (class 1259 OID 26387)
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
-- TOC entry 226 (class 1259 OID 26237)
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
-- TOC entry 227 (class 1259 OID 26246)
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
-- TOC entry 3528 (class 0 OID 0)
-- Dependencies: 227
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.news_id_seq OWNED BY public.news.id;


--
-- TOC entry 228 (class 1259 OID 26247)
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
-- TOC entry 229 (class 1259 OID 26251)
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
-- TOC entry 3529 (class 0 OID 0)
-- Dependencies: 229
-- Name: news_tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.news_tags_id_seq OWNED BY public.news_tags.id;


--
-- TOC entry 230 (class 1259 OID 26252)
-- Name: project_members; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_members (
    id integer NOT NULL,
    project_id integer NOT NULL,
    member_id integer NOT NULL
);


ALTER TABLE public.project_members OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 26255)
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
-- TOC entry 3530 (class 0 OID 0)
-- Dependencies: 231
-- Name: project_members_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_members_id_seq OWNED BY public.project_members.id;


--
-- TOC entry 232 (class 1259 OID 26256)
-- Name: project_tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_tags (
    project_id bigint NOT NULL,
    tag_id bigint NOT NULL
);


ALTER TABLE public.project_tags OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 26259)
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
-- TOC entry 234 (class 1259 OID 26268)
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
-- TOC entry 3531 (class 0 OID 0)
-- Dependencies: 234
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.projects_id_seq OWNED BY public.projects.id;


--
-- TOC entry 235 (class 1259 OID 26269)
-- Name: tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tags (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.tags OWNER TO postgres;

--
-- TOC entry 236 (class 1259 OID 26272)
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
-- TOC entry 3532 (class 0 OID 0)
-- Dependencies: 236
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tags_id_seq OWNED BY public.tags.id;


--
-- TOC entry 237 (class 1259 OID 26273)
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
-- TOC entry 238 (class 1259 OID 26281)
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
-- TOC entry 3533 (class 0 OID 0)
-- Dependencies: 238
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3283 (class 2604 OID 26603)
-- Name: activity_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs ALTER COLUMN id SET DEFAULT nextval('public.activity_logs_id_seq'::regclass);


--
-- TOC entry 3242 (class 2604 OID 26282)
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- TOC entry 3243 (class 2604 OID 26283)
-- Name: comments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments ALTER COLUMN id SET DEFAULT nextval('public.comments_id_seq'::regclass);


--
-- TOC entry 3247 (class 2604 OID 26284)
-- Name: feedback id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.feedback ALTER COLUMN id SET DEFAULT nextval('public.feedback_id_seq'::regclass);


--
-- TOC entry 3250 (class 2604 OID 26285)
-- Name: lab_profile id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile ALTER COLUMN id SET DEFAULT nextval('public.lab_profile_id_seq'::regclass);


--
-- TOC entry 3263 (class 2604 OID 26286)
-- Name: media_assets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.media_assets ALTER COLUMN id SET DEFAULT nextval('public.media_assets_id_seq'::regclass);


--
-- TOC entry 3265 (class 2604 OID 26287)
-- Name: members id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.members ALTER COLUMN id SET DEFAULT nextval('public.members_id_seq'::regclass);


--
-- TOC entry 3266 (class 2604 OID 26288)
-- Name: news id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news ALTER COLUMN id SET DEFAULT nextval('public.news_id_seq'::regclass);


--
-- TOC entry 3271 (class 2604 OID 26289)
-- Name: news_tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags ALTER COLUMN id SET DEFAULT nextval('public.news_tags_id_seq'::regclass);


--
-- TOC entry 3273 (class 2604 OID 26290)
-- Name: project_members id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members ALTER COLUMN id SET DEFAULT nextval('public.project_members_id_seq'::regclass);


--
-- TOC entry 3274 (class 2604 OID 26291)
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects ALTER COLUMN id SET DEFAULT nextval('public.projects_id_seq'::regclass);


--
-- TOC entry 3278 (class 2604 OID 26292)
-- Name: tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags ALTER COLUMN id SET DEFAULT nextval('public.tags_id_seq'::regclass);


--
-- TOC entry 3279 (class 2604 OID 26293)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3515 (class 0 OID 26600)
-- Dependencies: 241
-- Data for Name: activity_logs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.activity_logs (id, username, action, ip_address, device_info, created_at) FROM stdin;
1	Admin	Update Profil Lab	::1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36	2025-12-07 22:57:05.635303
2	admin2	Login Berhasil	::1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36	2025-12-08 07:39:26.92786
\.


--
-- TOC entry 3488 (class 0 OID 26184)
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
-- TOC entry 3490 (class 0 OID 26188)
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
-- TOC entry 3492 (class 0 OID 26198)
-- Dependencies: 218
-- Data for Name: feedback; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.feedback (id, nama_lengkap, email, subjek, pesan, created_at, is_read) FROM stdin;
78	Raihan	bujexixi@gmal.com	testing	Haiii aku raihan	2025-12-08 07:48:47.582192	f
\.


--
-- TOC entry 3494 (class 0 OID 26206)
-- Dependencies: 220
-- Data for Name: lab_profile; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lab_profile (id, visi, misi, sejarah, updated_at, alamat_lab, email_lab, telepon_lab, lokasi_lab, fb_link, x_link, ig_link, yt_link, linkedin, logo_path, hero_image_path, hero_title, hero_description, about_hero_image, news_hero_image, project_hero_image, gallery_hero_image, contact_hero_image, about_title, news_title, project_title, gallery_title, contact_title, footer_desc, copyright_text, struktur_org_path) FROM stdin;
2	Menjadi laboratorium multimedia dan mobile technology terdepan dalam inovasi dan kreativitas digital	Mengembangkan solusi digital inovatif melalui penelitian, pengembangan, dan kolaborasi dengan industri	Laboratorium Multimedia dan Mobile Tech didirikan pada tahun 2010 sebagai pusat pengembangan teknologi digital di Politeknik Negeri Malang	2025-11-17 13:52:20.720474+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	Profil Laboratorium	Berita & Kegiatan	Katalog Proyek	Galeri Multimedia	Kontak Kami	\N	\N	assets/uploads/struktur_1765123025.png
1	Menjadi laboratorium multimedia dan mobile technology terdepan dalam inovasi dan kreativitas digital	Mengembangkan solusi digital inovatif melalui penelitian, pengembangan, dan kolaborasi dengan industri	Laboratorium Multimedia dan Mobile Tech didirikan pada tahun 2010 sebagai pusat pengembangan teknologi digital di Politeknik Negeri Malang	2025-11-18 10:58:08.336706+07	Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141	multimedia@gmail.com	(0341) 404424	Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Malang	https://facebook.com/polinema.mobilelab	https://twitter.com/polinema_mobile	https://instagram.com/polinema.mobilelab	https://youtube.com/c/PolinemaMobileLab	https://linkedin.com/company/polinema-mobile-lab	assets/images/logo-placeholder.png	assets/images/hero.jpg	LABORATORIUM MOBILE AND MULTIMEDIA TECH	Pusat pengembangan karya inovatif di bidang UI/UX, Game, AR/VR.	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	Profil Laboratorium	Berita & Kegiatan	Katalog Proyek	Galeri Multimedia	Kontak Kami	\N	\N	assets/uploads/struktur_1765123025.png
\.


--
-- TOC entry 3496 (class 0 OID 26223)
-- Dependencies: 222
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
-- TOC entry 3498 (class 0 OID 26231)
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
-- TOC entry 3500 (class 0 OID 26237)
-- Dependencies: 226
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
-- TOC entry 3502 (class 0 OID 26247)
-- Dependencies: 228
-- Data for Name: news_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.news_tags (id, news_id, tag_id, created_at) FROM stdin;
\.


--
-- TOC entry 3504 (class 0 OID 26252)
-- Dependencies: 230
-- Data for Name: project_members; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_members (id, project_id, member_id) FROM stdin;
\.


--
-- TOC entry 3506 (class 0 OID 26256)
-- Dependencies: 232
-- Data for Name: project_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_tags (project_id, tag_id) FROM stdin;
\.


--
-- TOC entry 3507 (class 0 OID 26259)
-- Dependencies: 233
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
-- TOC entry 3509 (class 0 OID 26269)
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
-- TOC entry 3511 (class 0 OID 26273)
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
-- TOC entry 3534 (class 0 OID 0)
-- Dependencies: 240
-- Name: activity_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.activity_logs_id_seq', 2, true);


--
-- TOC entry 3535 (class 0 OID 0)
-- Dependencies: 215
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 12, true);


--
-- TOC entry 3536 (class 0 OID 0)
-- Dependencies: 217
-- Name: comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.comments_id_seq', 9, true);


--
-- TOC entry 3537 (class 0 OID 0)
-- Dependencies: 219
-- Name: feedback_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.feedback_id_seq', 78, true);


--
-- TOC entry 3538 (class 0 OID 0)
-- Dependencies: 221
-- Name: lab_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lab_profile_id_seq', 2, true);


--
-- TOC entry 3539 (class 0 OID 0)
-- Dependencies: 223
-- Name: media_assets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.media_assets_id_seq', 16, true);


--
-- TOC entry 3540 (class 0 OID 0)
-- Dependencies: 225
-- Name: members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.members_id_seq', 4, true);


--
-- TOC entry 3541 (class 0 OID 0)
-- Dependencies: 227
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.news_id_seq', 12, true);


--
-- TOC entry 3542 (class 0 OID 0)
-- Dependencies: 229
-- Name: news_tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.news_tags_id_seq', 1, false);


--
-- TOC entry 3543 (class 0 OID 0)
-- Dependencies: 231
-- Name: project_members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_members_id_seq', 2, true);


--
-- TOC entry 3544 (class 0 OID 0)
-- Dependencies: 234
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.projects_id_seq', 15, true);


--
-- TOC entry 3545 (class 0 OID 0)
-- Dependencies: 236
-- Name: tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tags_id_seq', 10, true);


--
-- TOC entry 3546 (class 0 OID 0)
-- Dependencies: 238
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 7, true);


--
-- TOC entry 3333 (class 2606 OID 26608)
-- Name: activity_logs activity_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_pkey PRIMARY KEY (id);


--
-- TOC entry 3289 (class 2606 OID 26295)
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- TOC entry 3291 (class 2606 OID 26297)
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- TOC entry 3293 (class 2606 OID 26299)
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- TOC entry 3295 (class 2606 OID 26301)
-- Name: feedback feedback_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.feedback
    ADD CONSTRAINT feedback_pkey PRIMARY KEY (id);


--
-- TOC entry 3297 (class 2606 OID 26303)
-- Name: lab_profile lab_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile
    ADD CONSTRAINT lab_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 3299 (class 2606 OID 26305)
-- Name: media_assets media_assets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.media_assets
    ADD CONSTRAINT media_assets_pkey PRIMARY KEY (id);


--
-- TOC entry 3301 (class 2606 OID 26307)
-- Name: members members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.members
    ADD CONSTRAINT members_pkey PRIMARY KEY (id);


--
-- TOC entry 3304 (class 2606 OID 26309)
-- Name: news news_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- TOC entry 3306 (class 2606 OID 26311)
-- Name: news news_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_slug_key UNIQUE (slug);


--
-- TOC entry 3309 (class 2606 OID 26313)
-- Name: news_tags news_tags_news_id_tag_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT news_tags_news_id_tag_id_key UNIQUE (news_id, tag_id);


--
-- TOC entry 3311 (class 2606 OID 26315)
-- Name: news_tags news_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT news_tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3313 (class 2606 OID 26317)
-- Name: project_members project_members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_pkey PRIMARY KEY (id);


--
-- TOC entry 3315 (class 2606 OID 26319)
-- Name: project_tags project_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT project_tags_pkey PRIMARY KEY (project_id, tag_id);


--
-- TOC entry 3318 (class 2606 OID 26321)
-- Name: projects projects_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 3320 (class 2606 OID 26323)
-- Name: projects projects_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_slug_key UNIQUE (slug);


--
-- TOC entry 3322 (class 2606 OID 26325)
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3324 (class 2606 OID 26327)
-- Name: tags tags_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_slug_key UNIQUE (slug);


--
-- TOC entry 3326 (class 2606 OID 26329)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3328 (class 2606 OID 26331)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3302 (class 1259 OID 26332)
-- Name: idx_news_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_news_created_at ON public.news USING btree (created_at);


--
-- TOC entry 3307 (class 1259 OID 26333)
-- Name: idx_news_tags_tag; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_news_tags_tag ON public.news_tags USING btree (tag_id);


--
-- TOC entry 3316 (class 1259 OID 26334)
-- Name: idx_projects_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_projects_created_at ON public.projects USING btree (created_at);


--
-- TOC entry 3329 (class 1259 OID 26394)
-- Name: mv_feedback_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX mv_feedback_created ON public.mv_feedback_summary USING btree (created_epoch);


--
-- TOC entry 3330 (class 1259 OID 26393)
-- Name: mv_feedback_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX mv_feedback_id ON public.mv_feedback_summary USING btree (id);


--
-- TOC entry 3331 (class 1259 OID 26395)
-- Name: mv_feedback_read; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX mv_feedback_read ON public.mv_feedback_summary USING btree (is_read);


--
-- TOC entry 3344 (class 2620 OID 26335)
-- Name: comments update_comments_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_comments_updated_at BEFORE UPDATE ON public.comments FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3334 (class 2606 OID 26336)
-- Name: comments fk_comments_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3336 (class 2606 OID 26341)
-- Name: news_tags fk_news_tags_news; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT fk_news_tags_news FOREIGN KEY (news_id) REFERENCES public.news(id) ON DELETE CASCADE;


--
-- TOC entry 3337 (class 2606 OID 26346)
-- Name: news_tags fk_news_tags_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT fk_news_tags_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3335 (class 2606 OID 26351)
-- Name: news fk_news_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT fk_news_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3342 (class 2606 OID 26356)
-- Name: projects fk_projects_category; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_category FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- TOC entry 3340 (class 2606 OID 26361)
-- Name: project_tags fk_projects_tags_project; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_projects_tags_project FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3341 (class 2606 OID 26366)
-- Name: project_tags fk_projects_tags_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_projects_tags_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3343 (class 2606 OID 26371)
-- Name: projects fk_projects_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3338 (class 2606 OID 26376)
-- Name: project_members project_members_member_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_member_id_fkey FOREIGN KEY (member_id) REFERENCES public.members(id) ON DELETE CASCADE;


--
-- TOC entry 3339 (class 2606 OID 26381)
-- Name: project_members project_members_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_project_id_fkey FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3513 (class 0 OID 26387)
-- Dependencies: 239 3517
-- Name: mv_feedback_summary; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: postgres
--

REFRESH MATERIALIZED VIEW public.mv_feedback_summary;


-- Completed on 2025-12-08 07:56:07

--
-- PostgreSQL database dump complete
--

\unrestrict LPcBdaJEsvt6xWYoZaHLVIanKkVadGOPMuiuaSQJEXyrWJJSHsCSdKYhCUR8UYx

