--
-- PostgreSQL database dump
--

\restrict BebfVdlCzSb2Z7mHnrkDgkeNYenYJxaC9Jn5yPEgaDCKyyEgaFN0xhjmpejCvFZ

-- Dumped from database version 15.14
-- Dumped by pg_dump version 15.14

-- Started on 2025-11-25 07:34:05

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
-- TOC entry 243 (class 1255 OID 25933)
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
-- TOC entry 214 (class 1259 OID 25934)
-- Name: audit_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.audit_logs (
    id integer NOT NULL,
    user_id integer,
    action character varying(100) NOT NULL,
    entity_type character varying(50) NOT NULL,
    entity_id integer,
    old_values json,
    new_values json,
    ip_address character varying(45),
    user_agent text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.audit_logs OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 25940)
-- Name: audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.audit_logs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.audit_logs_id_seq OWNER TO postgres;

--
-- TOC entry 3527 (class 0 OID 0)
-- Dependencies: 215
-- Name: audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.audit_logs_id_seq OWNED BY public.audit_logs.id;


--
-- TOC entry 216 (class 1259 OID 25941)
-- Name: categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categories (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.categories OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 25944)
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
-- TOC entry 3528 (class 0 OID 0)
-- Dependencies: 217
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- TOC entry 218 (class 1259 OID 25945)
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
    CONSTRAINT comments_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.comments OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 25954)
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
-- TOC entry 3529 (class 0 OID 0)
-- Dependencies: 219
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.comments_id_seq OWNED BY public.comments.id;


--
-- TOC entry 220 (class 1259 OID 25955)
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
-- TOC entry 221 (class 1259 OID 25962)
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
-- TOC entry 3530 (class 0 OID 0)
-- Dependencies: 221
-- Name: feedback_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.feedback_id_seq OWNED BY public.feedback.id;


--
-- TOC entry 222 (class 1259 OID 25963)
-- Name: galleries; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.galleries (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    image_path character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    status character varying(20) DEFAULT 'published'::character varying,
    category character varying(100),
    event_name character varying(150)
);


ALTER TABLE public.galleries OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 25970)
-- Name: galleries_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.galleries_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.galleries_id_seq OWNER TO postgres;

--
-- TOC entry 3531 (class 0 OID 0)
-- Dependencies: 223
-- Name: galleries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.galleries_id_seq OWNED BY public.galleries.id;


--
-- TOC entry 224 (class 1259 OID 25971)
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
-- TOC entry 225 (class 1259 OID 25977)
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
-- TOC entry 3532 (class 0 OID 0)
-- Dependencies: 225
-- Name: lab_profile_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.lab_profile_id_seq OWNED BY public.lab_profile.id;


--
-- TOC entry 226 (class 1259 OID 25978)
-- Name: media_assets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.media_assets (
    id bigint NOT NULL,
    type character varying(50),
    url character varying(2048) NOT NULL,
    caption character varying(512),
    created_at timestamp with time zone DEFAULT now(),
    deskripsi text
);


ALTER TABLE public.media_assets OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 25984)
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
-- TOC entry 3533 (class 0 OID 0)
-- Dependencies: 227
-- Name: media_assets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.media_assets_id_seq OWNED BY public.media_assets.id;


--
-- TOC entry 228 (class 1259 OID 25985)
-- Name: members; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.members (
    id bigint NOT NULL,
    name character varying(200) NOT NULL,
    role character varying(150),
    avatar_url character varying(1024),
    linkedin_url character varying(1024),
    tags character varying(255),
    scholar_url character varying(1024)
);


ALTER TABLE public.members OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 25990)
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
-- TOC entry 3534 (class 0 OID 0)
-- Dependencies: 229
-- Name: members_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.members_id_seq OWNED BY public.members.id;


--
-- TOC entry 230 (class 1259 OID 25991)
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
    type character varying(20) DEFAULT 'news'::character varying
);


ALTER TABLE public.news OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 26000)
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
-- TOC entry 3535 (class 0 OID 0)
-- Dependencies: 231
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.news_id_seq OWNED BY public.news.id;


--
-- TOC entry 232 (class 1259 OID 26001)
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
-- TOC entry 233 (class 1259 OID 26005)
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
-- TOC entry 3536 (class 0 OID 0)
-- Dependencies: 233
-- Name: news_tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.news_tags_id_seq OWNED BY public.news_tags.id;


--
-- TOC entry 234 (class 1259 OID 26006)
-- Name: project_members; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_members (
    id integer NOT NULL,
    project_id integer NOT NULL,
    member_id integer NOT NULL
);


ALTER TABLE public.project_members OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 26009)
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
-- TOC entry 3537 (class 0 OID 0)
-- Dependencies: 235
-- Name: project_members_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_members_id_seq OWNED BY public.project_members.id;


--
-- TOC entry 236 (class 1259 OID 26010)
-- Name: project_tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_tags (
    project_id bigint NOT NULL,
    tag_id bigint NOT NULL
);


ALTER TABLE public.project_tags OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 26013)
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
    user_id integer
);


ALTER TABLE public.projects OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 26021)
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
-- TOC entry 3538 (class 0 OID 0)
-- Dependencies: 238
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.projects_id_seq OWNED BY public.projects.id;


--
-- TOC entry 239 (class 1259 OID 26022)
-- Name: tags; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tags (
    id bigint NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(150) NOT NULL
);


ALTER TABLE public.tags OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 26025)
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
-- TOC entry 3539 (class 0 OID 0)
-- Dependencies: 240
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tags_id_seq OWNED BY public.tags.id;


--
-- TOC entry 241 (class 1259 OID 26026)
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
-- TOC entry 242 (class 1259 OID 26034)
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
-- TOC entry 3540 (class 0 OID 0)
-- Dependencies: 242
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3243 (class 2604 OID 26035)
-- Name: audit_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs ALTER COLUMN id SET DEFAULT nextval('public.audit_logs_id_seq'::regclass);


--
-- TOC entry 3245 (class 2604 OID 26036)
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- TOC entry 3246 (class 2604 OID 26037)
-- Name: comments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments ALTER COLUMN id SET DEFAULT nextval('public.comments_id_seq'::regclass);


--
-- TOC entry 3250 (class 2604 OID 26038)
-- Name: feedback id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.feedback ALTER COLUMN id SET DEFAULT nextval('public.feedback_id_seq'::regclass);


--
-- TOC entry 3253 (class 2604 OID 26039)
-- Name: galleries id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galleries ALTER COLUMN id SET DEFAULT nextval('public.galleries_id_seq'::regclass);


--
-- TOC entry 3256 (class 2604 OID 26040)
-- Name: lab_profile id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile ALTER COLUMN id SET DEFAULT nextval('public.lab_profile_id_seq'::regclass);


--
-- TOC entry 3268 (class 2604 OID 26041)
-- Name: media_assets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.media_assets ALTER COLUMN id SET DEFAULT nextval('public.media_assets_id_seq'::regclass);


--
-- TOC entry 3270 (class 2604 OID 26042)
-- Name: members id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.members ALTER COLUMN id SET DEFAULT nextval('public.members_id_seq'::regclass);


--
-- TOC entry 3271 (class 2604 OID 26043)
-- Name: news id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news ALTER COLUMN id SET DEFAULT nextval('public.news_id_seq'::regclass);


--
-- TOC entry 3276 (class 2604 OID 26044)
-- Name: news_tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags ALTER COLUMN id SET DEFAULT nextval('public.news_tags_id_seq'::regclass);


--
-- TOC entry 3278 (class 2604 OID 26045)
-- Name: project_members id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members ALTER COLUMN id SET DEFAULT nextval('public.project_members_id_seq'::regclass);


--
-- TOC entry 3279 (class 2604 OID 26046)
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects ALTER COLUMN id SET DEFAULT nextval('public.projects_id_seq'::regclass);


--
-- TOC entry 3283 (class 2604 OID 26047)
-- Name: tags id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags ALTER COLUMN id SET DEFAULT nextval('public.tags_id_seq'::regclass);


--
-- TOC entry 3284 (class 2604 OID 26048)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3493 (class 0 OID 25934)
-- Dependencies: 214
-- Data for Name: audit_logs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.audit_logs (id, user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent, created_at) FROM stdin;
\.


--
-- TOC entry 3495 (class 0 OID 25941)
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
-- TOC entry 3497 (class 0 OID 25945)
-- Dependencies: 218
-- Data for Name: comments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.comments (id, entity_type, entity_id, author_name, author_email, rating, content, status, created_at, updated_at, user_id) FROM stdin;
\.


--
-- TOC entry 3499 (class 0 OID 25955)
-- Dependencies: 220
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
21	Goblokk	jkdvbsjkvb@gmail.com	Ogryzek	wviowrvbjkbejkvbkejbvkjvbjdqwqdw	2025-11-18 13:51:07.543544	f
22	Goblokk	jkdvbsjkvb@gmail.com	Ogryzek	wviowrvbjkbejkvbkejbvkjvbjdqwqdw	2025-11-18 13:52:41.557461	f
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
38	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:14:19.057673	f
39	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:16:21.388797	f
40	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:19:00.420605	f
41	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:19:10.183241	f
42	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:21:23.915341	f
43	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:23:06.535536	f
44	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:23:50.032986	f
45	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:25:59.549504	f
46	DIANCOK	acokkk@gmail.com	reitherseitiheruthreuthrueheret	qwertyuiopasdfghjklsdfvdjv fjb	2025-11-18 14:30:12.090987	f
47	MA Qklkoko	jqbdjb@gmail.com	reitherseitiheruthreuthrueheretduigdyu	qwdhvwqhjdvehjdvewhjwdvfhjewvfhjewvfhjhewvfew	2025-11-18 14:30:32.230705	f
48	MA Qklkoko	jqbdjb@gmail.com	reitherseitiheruthreuthrueheretduigdyu	qwdhvwqhjdvehjdvewhjwdvfhjewvfhjewvfhjhewvfew	2025-11-18 14:32:15.28086	f
49	MA Qhhh	adamkill36@gmail.com	wqujdhwqudhqwuhewuf	dbwqifbqwjifeWGVGVIOSGVOIEDWHFHWEIOHFE	2025-11-18 14:32:28.878134	f
50	MA Qhhh	adamkill36@gmail.com	wqujdhwqudhqwuhewuf	dbwqifbqwjifeWGVGVIOSGVOIEDWHFHWEIOHFE	2025-11-18 14:33:34.087873	f
51	MA Q))))	IJIIJI@gmail.com	sfgndrnrininirfhb	ewdecwtyufbiodrzngodfnbiodfngiodfngiodd	2025-11-18 14:33:57.955563	f
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
71	MA Qnjnjn	ejbfjke@gmail.com	Crypto	asdfghjwkwsmnfmvcshjdvjhdsvdshk	2025-11-18 16:09:54.963203	f
72	MA QjnjnWKWKWK	dhgasd@gmail.com	wqujdhwqudhqwuhewuf	qdbeqwuifewvfyufewuifvewuifewvbfo3bvuof3w	2025-11-18 18:02:59.821321	f
73	MA QjnjnWKWKWK	dhgasd@gmail.com	wqujdhwqudhqwuhewuf	qdbeqwuifewvfyufewuifvewuifewvbfo3bvuof3w	2025-11-18 18:09:43.100482	f
74	MA Qvhvh	RAWR@gmail.com	diuvqudvq3udvucvdyu21	sjkjbcehj cehj ew vuyvdg23idg2gdg2qd	2025-11-18 18:10:05.649432	f
\.


--
-- TOC entry 3501 (class 0 OID 25963)
-- Dependencies: 222
-- Data for Name: galleries; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.galleries (id, title, description, image_path, created_at, status, category, event_name) FROM stdin;
2	Kegiatan Sosialisasi	Kegiatan pengenalan jurusan Sistem Informasi kepada siswa SMA.	uploads/galeri2.jpg	2025-11-10 21:33:33.628449	published	\N	\N
3	Pelatihan IoT	Workshop Internet of Things untuk mahasiswa baru.	uploads/galeri3.jpg	2025-11-10 21:33:33.628449	draft	\N	\N
1	Pameran Teknologi 2025	Dokumentasi acara pameran tahunan di kampus.	uploads/galeri1.jpg	2025-11-10 21:33:33.628449	published	Pameran Teknologi	Pameran Inovasi Kampus
\.


--
-- TOC entry 3503 (class 0 OID 25971)
-- Dependencies: 224
-- Data for Name: lab_profile; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lab_profile (id, visi, misi, sejarah, updated_at, alamat_lab, email_lab, telepon_lab, lokasi_lab, fb_link, x_link, ig_link, yt_link, linkedin, logo_path, hero_image_path, hero_title, hero_description, about_hero_image, news_hero_image, project_hero_image, gallery_hero_image, contact_hero_image, about_title, news_title, project_title, gallery_title, contact_title) FROM stdin;
2	Menjadi laboratorium multimedia dan mobile technology terdepan dalam inovasi dan kreativitas digital	Mengembangkan solusi digital inovatif melalui penelitian, pengembangan, dan kolaborasi dengan industri	Laboratorium Multimedia dan Mobile Tech didirikan pada tahun 2010 sebagai pusat pengembangan teknologi digital di Politeknik Negeri Malang	2025-11-17 13:52:20.720474+07	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	Profil Laboratorium	Berita & Kegiatan	Katalog Proyek	Galeri Multimedia	Kontak Kami
1	Menjadi lab unggulan	Meningkatkan riset dan inovasi	Didirikan tahun 2020	2025-11-18 10:58:08.336706+07	Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141	mobile.multimedia@polinema.ac.id	(0341) 404424	Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Malang	https://facebook.com/polinema.mobilelab	https://twitter.com/polinema_mobile	https://instagram.com/polinema.mobilelab	https://youtube.com/c/PolinemaMobileLab	https://linkedin.com/company/polinema-mobile-lab	assets/images/logo-placeholder.png	assets/images/hero.jpg	LABORATORIUM MOBILE AND MULTIMEDIA TECH	Pusat pengembangan karya inovatif di bidang UI/UX, Game, AR/VR.	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	assets/images/hero.jpg	Profil Laboratorium	Berita & Kegiatan	Katalog Proyek	Galeri Multimedia	Kontak Kami
\.


--
-- TOC entry 3505 (class 0 OID 25978)
-- Dependencies: 226
-- Data for Name: media_assets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.media_assets (id, type, url, caption, created_at, deskripsi) FROM stdin;
1	image	../assets/images/gallery/1.jpg	Workshop Pengembangan Aplikasi Android	2025-11-10 19:14:16.769618+07	\N
2	image	../assets/images/gallery/2.jpg	Kegiatan Pameran Multimedia 2025	2025-11-10 19:14:16.769618+07	\N
3	video	../assets/videos/demo-project.mp4	Demo Sistem Mobile Smart Attendance	2025-11-10 19:14:16.769618+07	\N
4	foto	https://files.catbox.moe/vgd0xe.png	Workshop AI di Polinema	2025-11-12 10:00:00+07	\N
5	foto	https://files.catbox.moe/vgd0xe.png	Workshop AI di Polinema	2025-11-12 10:00:00+07	Kegiatan workshop pengenalan teknologi Artificial Intelligence bersama mahasiswa jurusan Multimedia MMT POLINEMA. Acara ini bertujuan untuk memperkenalkan penerapan AI dalam dunia industri kreatif dan pendidikan.
6	foto	assets/images/galeri/gallery-photo-1.jpg	Kegiatan Dies Natalis	2025-11-17 13:41:05.691975+07	Hari ulang tahun kampus
7	foto	assets/images/galeri/gallery-photo-2.jpg	Wisuda Mahasiswa	2025-11-17 13:41:05.691975+07	Hari kelulusan mahasiswa
8	video	https://www.youtube.com/embed/abcdefghij	Video Dokumentasi Lomba Game Dev	2025-11-17 13:41:05.691975+07	Lomba Game 2025
\.


--
-- TOC entry 3507 (class 0 OID 25985)
-- Dependencies: 228
-- Data for Name: members; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.members (id, name, role, avatar_url, linkedin_url, tags, scholar_url) FROM stdin;
1	Rizky Ananda	Ketua Laboratorium	images/team/rizky.jpg	https://linkedin.com/in/rizkyananda	\N	\N
2	Nadia Putri	Wakil Ketua	images/team/nadia.jpg	https://linkedin.com/in/nadiaputri	\N	\N
3	Fajar Pratama	Anggota	images/team/fajar.jpg	https://linkedin.com/in/fajarpratama	\N	\N
4	Dewi Lestari	Sekretaris	images/team/dewi.jpg	https://linkedin.com/in/dewilestari	\N	\N
\.


--
-- TOC entry 3509 (class 0 OID 25991)
-- Dependencies: 230
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.news (id, title, slug, summary, content, cover_image, status, created_at, updated_at, user_id, type) FROM stdin;
1	Workshop Flutter 2025	workshop-flutter-2025	Pelatihan pengembangan aplikasi Flutter untuk mahasiswa tingkat akhir.	Laboratorium Mobile & Multimedia Tech mengadakan workshop pengembangan aplikasi Flutter untuk membantu mahasiswa memahami dasar cross-platform mobile app development.	../assets/images/news/flutter-workshop.jpg	1	2025-11-10 19:15:15.466339+07	2025-11-10 19:15:15.466339+07	\N	news
2	AI dan Multimedia: Kolaborasi Masa Depan	ai-dan-multimedia	Membahas potensi integrasi AI dalam teknologi multimedia modern.	Seminar AI dan Multimedia membahas penerapan machine learning dalam pengolahan gambar dan video untuk meningkatkan pengalaman pengguna.	../assets/images/news/ai-multimedia.jpg	1	2025-11-10 19:15:28.421072+07	2025-11-10 19:15:28.421072+07	\N	news
3	Pameran Proyek Akhir Mahasiswa	pameran-proyek-akhir	Pameran tahunan karya mahasiswa di bidang mobile dan multimedia.	Acara ini menampilkan hasil riset dan proyek inovatif mahasiswa yang dikembangkan di Laboratorium Mobile and Multimedia Tech.	../assets/images/news/exhibition.jpg	1	2025-11-10 19:15:46.951029+07	2025-11-10 19:15:46.951029+07	\N	news
4	Pelatihan Internet of Things (IoT) untuk Dosen dan Mahasiswa	pelatihan-iot	Pelatihan ini memperkenalkan konsep dasar dan penerapan IoT.	Kegiatan pelatihan ini melibatkan dosen dan mahasiswa untuk memahami penerapan IoT dalam sistem pintar, serta implementasi sensor dan mikrokontroler untuk proyek real-time.	../assets/images/news/pelatihan-iot.jpg	published	2025-11-10 19:28:25.7442+07	2025-11-10 19:28:25.7442+07	\N	news
5	Kuliah Tamu: Inovasi Teknologi AI di Dunia Industri	kuliah-tamu-ai	Kegiatan kuliah tamu membahas penerapan AI dalam dunia industri modern.	Departemen Sistem Informasi mengadakan kuliah tamu bertema “Inovasi Teknologi AI di Dunia Industri” yang menghadirkan pembicara dari startup teknologi ternama. Kegiatan ini bertujuan memperkenalkan mahasiswa pada perkembangan terbaru dalam teknologi kecerdasan buatan.	../assets/images/news/kuliah-tamu-ai.jpg	published	2025-11-10 19:28:40.780585+07	2025-11-10 19:28:40.780585+07	\N	news
6	Workshop UI/UX Design untuk Mahasiswa	workshop-uiux	Workshop ini mengajarkan dasar-dasar desain antarmuka dan pengalaman pengguna.	Mahasiswa belajar langsung konsep desain UI/UX dari praktisi industri. Workshop ini juga membahas pentingnya user-centered design dalam pengembangan aplikasi modern.	../assets/images/news/workshop-uiux.jpg	published	2025-11-10 19:28:57.473885+07	2025-11-10 19:28:57.473885+07	\N	news
7	Rapat Persiapan Kompetisi Gemastik 2025	rapat-persiapan-kompetisi-gemastik-2025	Rapat koordinasi internal menjelang kompetisi Gemastik tahun 2025.	Seluruh tim dari Laboratorium Mobile and Multimedia Tech melakukan rapat persiapan untuk mengikuti Gemastik 2025. Fokus utama adalah pengembangan aplikasi dengan teknologi terbaru dan manajemen tim yang solid.	../assets/images/news/gemastik-2025.jpg	published	2025-11-10 19:29:10.914884+07	2025-11-24 01:27:03.440257+07	\N	news
\.


--
-- TOC entry 3511 (class 0 OID 26001)
-- Dependencies: 232
-- Data for Name: news_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.news_tags (id, news_id, tag_id, created_at) FROM stdin;
\.


--
-- TOC entry 3513 (class 0 OID 26006)
-- Dependencies: 234
-- Data for Name: project_members; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_members (id, project_id, member_id) FROM stdin;
1	1	1
2	1	2
\.


--
-- TOC entry 3515 (class 0 OID 26010)
-- Dependencies: 236
-- Data for Name: project_tags; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_tags (project_id, tag_id) FROM stdin;
\.


--
-- TOC entry 3516 (class 0 OID 26013)
-- Dependencies: 237
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.projects (id, title, slug, summary, description, year, cover_image, repo_url, demo_url, status, created_at, updated_at, category_id, user_id) FROM stdin;
1	IoT Smart Home Controller	iot-smart-home	Sistem pengendali rumah pintar berbasis IoT.	Proyek ini memungkinkan pengguna mengontrol perangkat rumah tangga dari jarak jauh melalui aplikasi mobile dan integrasi sensor IoT.	2023	../assets/images/projects/smarthome.jpg	https://github.com/labpolinema/smarthome	https://demo.labpolinema.ac.id/smarthome	1	2025-11-10 19:18:16.366231+07	2025-11-10 19:18:16.366231+07	4	\N
2	Augmented Reality Learning App	ar-learning-app	Aplikasi pembelajaran interaktif berbasis AR untuk anak sekolah.	Proyek ini menggabungkan teknologi Augmented Reality untuk menciptakan pengalaman belajar yang menyenangkan dan imersif.	2024	../assets/images/projects/ar-learning.jpg	https://github.com/labpolinema/ar-learning	https://demo.labpolinema.ac.id/ar-learning	1	2025-11-10 19:18:30.048209+07	2025-11-10 19:18:30.048209+07	2	\N
4	Smart Attendance System	smart-attendance	Aplikasi presensi berbasis pengenalan wajah untuk perkuliahan.	Proyek ini menggunakan teknologi face recognition untuk mencatat kehadiran mahasiswa secara otomatis dan real-time. Sistem ini dikembangkan menggunakan Python dan database PostgreSQL.	2025	../assets/images/projects/attendance.jpg	https://github.com/labpolinema/smart-attendance	https://demo.labpolinema.ac.id/attendance	published	2025-11-10 19:31:01.213185+07	2025-11-10 19:31:01.213185+07	1	\N
6	Virtual Reality Tourism Experience	vr-tourism	Aplikasi tur virtual untuk destinasi wisata Indonesia.	Aplikasi VR ini menghadirkan pengalaman menjelajahi tempat wisata Indonesia dengan teknologi virtual reality, dikembangkan menggunakan Unity dan Oculus SDK.	2024	../assets/images/projects/vr-tourism.jpg	https://github.com/labpolinema/vr-tourism	https://demo.labpolinema.ac.id/vr-tourism	published	2025-11-10 19:31:42.529039+07	2025-11-10 19:31:42.529039+07	4	\N
3	Game Edukasi Bahasa Indonesia	game-edukasi-bahasa-indonesia	Game ini dirancang untuk membantu anak-anak belajar kosakata dan tata bahasa melalui permainan interaktif yang menyenangkan dan edukatif.	Game ini dirancang untuk membantu anak-anak belajar kosakata dan tata bahasa melalui permainan interaktif yang menyenangkan dan edukatif.	2025	../assets/images/projects/game-edukasi.jpg	https://github.com/labpolinema/game-edukasi	https://demo.labpolinema.ac.id/game-edukasi	published	2025-11-10 19:30:41.150439+07	2025-11-24 01:30:19.435286+07	2	\N
\.


--
-- TOC entry 3518 (class 0 OID 26022)
-- Dependencies: 239
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
-- TOC entry 3520 (class 0 OID 26026)
-- Dependencies: 241
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, password, role, created_at, updated_at) FROM stdin;
2	Editor 1	editor1@labpolinema.ac.id	$2y$10$abc1234567890examplehashhashhash	editor	2025-11-10 19:13:54.974842+07	2025-11-10 19:13:54.974842+07
3	adam	adamkian09@gmail.com	1234	anggota	2025-11-17 20:57:16.471984+07	2025-11-17 20:57:16.471984+07
5	adam	adamkull36@gmail.com	1234	anggota	2025-11-17 21:16:53.032775+07	2025-11-17 21:16:53.032775+07
1	Admin Lab	admin@labpolinema.ac.id	admin123	admin	2025-11-10 19:13:54.974842+07	2025-11-10 19:13:54.974842+07
6	admin2	admin2@dummy.com	12345	admin	2025-11-22 20:52:31.324255+07	2025-11-22 20:52:31.324255+07
\.


--
-- TOC entry 3541 (class 0 OID 0)
-- Dependencies: 215
-- Name: audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.audit_logs_id_seq', 1, false);


--
-- TOC entry 3542 (class 0 OID 0)
-- Dependencies: 217
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categories_id_seq', 12, true);


--
-- TOC entry 3543 (class 0 OID 0)
-- Dependencies: 219
-- Name: comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.comments_id_seq', 1, false);


--
-- TOC entry 3544 (class 0 OID 0)
-- Dependencies: 221
-- Name: feedback_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.feedback_id_seq', 74, true);


--
-- TOC entry 3545 (class 0 OID 0)
-- Dependencies: 223
-- Name: galleries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.galleries_id_seq', 3, true);


--
-- TOC entry 3546 (class 0 OID 0)
-- Dependencies: 225
-- Name: lab_profile_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.lab_profile_id_seq', 2, true);


--
-- TOC entry 3547 (class 0 OID 0)
-- Dependencies: 227
-- Name: media_assets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.media_assets_id_seq', 9, true);


--
-- TOC entry 3548 (class 0 OID 0)
-- Dependencies: 229
-- Name: members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.members_id_seq', 4, true);


--
-- TOC entry 3549 (class 0 OID 0)
-- Dependencies: 231
-- Name: news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.news_id_seq', 7, true);


--
-- TOC entry 3550 (class 0 OID 0)
-- Dependencies: 233
-- Name: news_tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.news_tags_id_seq', 1, false);


--
-- TOC entry 3551 (class 0 OID 0)
-- Dependencies: 235
-- Name: project_members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_members_id_seq', 2, true);


--
-- TOC entry 3552 (class 0 OID 0)
-- Dependencies: 238
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.projects_id_seq', 7, true);


--
-- TOC entry 3553 (class 0 OID 0)
-- Dependencies: 240
-- Name: tags_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tags_id_seq', 10, true);


--
-- TOC entry 3554 (class 0 OID 0)
-- Dependencies: 242
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 6, true);


--
-- TOC entry 3290 (class 2606 OID 26050)
-- Name: audit_logs audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_pkey PRIMARY KEY (id);


--
-- TOC entry 3293 (class 2606 OID 26052)
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- TOC entry 3295 (class 2606 OID 26054)
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- TOC entry 3297 (class 2606 OID 26056)
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- TOC entry 3299 (class 2606 OID 26058)
-- Name: feedback feedback_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.feedback
    ADD CONSTRAINT feedback_pkey PRIMARY KEY (id);


--
-- TOC entry 3301 (class 2606 OID 26060)
-- Name: galleries galleries_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.galleries
    ADD CONSTRAINT galleries_pkey PRIMARY KEY (id);


--
-- TOC entry 3303 (class 2606 OID 26062)
-- Name: lab_profile lab_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lab_profile
    ADD CONSTRAINT lab_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 3305 (class 2606 OID 26064)
-- Name: media_assets media_assets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.media_assets
    ADD CONSTRAINT media_assets_pkey PRIMARY KEY (id);


--
-- TOC entry 3307 (class 2606 OID 26066)
-- Name: members members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.members
    ADD CONSTRAINT members_pkey PRIMARY KEY (id);


--
-- TOC entry 3310 (class 2606 OID 26068)
-- Name: news news_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- TOC entry 3312 (class 2606 OID 26070)
-- Name: news news_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT news_slug_key UNIQUE (slug);


--
-- TOC entry 3315 (class 2606 OID 26072)
-- Name: news_tags news_tags_news_id_tag_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT news_tags_news_id_tag_id_key UNIQUE (news_id, tag_id);


--
-- TOC entry 3317 (class 2606 OID 26074)
-- Name: news_tags news_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT news_tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3319 (class 2606 OID 26076)
-- Name: project_members project_members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_pkey PRIMARY KEY (id);


--
-- TOC entry 3321 (class 2606 OID 26078)
-- Name: project_tags project_tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT project_tags_pkey PRIMARY KEY (project_id, tag_id);


--
-- TOC entry 3324 (class 2606 OID 26080)
-- Name: projects projects_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 3326 (class 2606 OID 26082)
-- Name: projects projects_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_slug_key UNIQUE (slug);


--
-- TOC entry 3328 (class 2606 OID 26084)
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_pkey PRIMARY KEY (id);


--
-- TOC entry 3330 (class 2606 OID 26086)
-- Name: tags tags_slug_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tags
    ADD CONSTRAINT tags_slug_key UNIQUE (slug);


--
-- TOC entry 3332 (class 2606 OID 26088)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3334 (class 2606 OID 26090)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3291 (class 1259 OID 26091)
-- Name: idx_audit_logs_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_audit_logs_created_at ON public.audit_logs USING btree (created_at);


--
-- TOC entry 3308 (class 1259 OID 26092)
-- Name: idx_news_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_news_created_at ON public.news USING btree (created_at);


--
-- TOC entry 3313 (class 1259 OID 26093)
-- Name: idx_news_tags_tag; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_news_tags_tag ON public.news_tags USING btree (tag_id);


--
-- TOC entry 3322 (class 1259 OID 26094)
-- Name: idx_projects_created_at; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_projects_created_at ON public.projects USING btree (created_at);


--
-- TOC entry 3350 (class 2620 OID 26095)
-- Name: comments update_comments_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_comments_updated_at BEFORE UPDATE ON public.comments FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- TOC entry 3335 (class 2606 OID 26096)
-- Name: audit_logs fk_audit_logs_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3336 (class 2606 OID 26101)
-- Name: comments fk_comments_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3338 (class 2606 OID 26106)
-- Name: news_tags fk_news_tags_news; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT fk_news_tags_news FOREIGN KEY (news_id) REFERENCES public.news(id) ON DELETE CASCADE;


--
-- TOC entry 3339 (class 2606 OID 26111)
-- Name: news_tags fk_news_tags_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news_tags
    ADD CONSTRAINT fk_news_tags_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3337 (class 2606 OID 26116)
-- Name: news fk_news_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.news
    ADD CONSTRAINT fk_news_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3348 (class 2606 OID 26121)
-- Name: projects fk_projects_category; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_category FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- TOC entry 3340 (class 2606 OID 26126)
-- Name: project_members fk_projects_members_member; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT fk_projects_members_member FOREIGN KEY (member_id) REFERENCES public.members(id) ON DELETE CASCADE;


--
-- TOC entry 3341 (class 2606 OID 26131)
-- Name: project_members fk_projects_members_project; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT fk_projects_members_project FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3344 (class 2606 OID 26136)
-- Name: project_tags fk_projects_tags_project; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_projects_tags_project FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3345 (class 2606 OID 26141)
-- Name: project_tags fk_projects_tags_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_projects_tags_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3349 (class 2606 OID 26146)
-- Name: projects fk_projects_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT fk_projects_user FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3346 (class 2606 OID 26151)
-- Name: project_tags fk_pt_project; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_pt_project FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


--
-- TOC entry 3347 (class 2606 OID 26156)
-- Name: project_tags fk_pt_tag; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_tags
    ADD CONSTRAINT fk_pt_tag FOREIGN KEY (tag_id) REFERENCES public.tags(id) ON DELETE CASCADE;


--
-- TOC entry 3342 (class 2606 OID 26161)
-- Name: project_members project_members_member_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_member_id_fkey FOREIGN KEY (member_id) REFERENCES public.members(id) ON DELETE CASCADE;


--
-- TOC entry 3343 (class 2606 OID 26166)
-- Name: project_members project_members_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_members
    ADD CONSTRAINT project_members_project_id_fkey FOREIGN KEY (project_id) REFERENCES public.projects(id) ON DELETE CASCADE;


-- Completed on 2025-11-25 07:34:07

--
-- PostgreSQL database dump complete
--

\unrestrict BebfVdlCzSb2Z7mHnrkDgkeNYenYJxaC9Jn5yPEgaDCKyyEgaFN0xhjmpejCvFZ

