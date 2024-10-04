--
-- PostgreSQL database dump
--

-- Dumped from database version 12.20 (Ubuntu 12.20-0ubuntu0.20.04.1)
-- Dumped by pg_dump version 12.20 (Ubuntu 12.20-0ubuntu0.20.04.1)

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
-- Name: bodega_central; Type: SCHEMA; Schema: -; Owner: pgadmin
--

CREATE SCHEMA bodega_central;


ALTER SCHEMA bodega_central OWNER TO pgadmin;

--
-- Name: articulos_art_id_seq; Type: SEQUENCE; Schema: bodega_central; Owner: pgadmin
--

CREATE SEQUENCE bodega_central.articulos_art_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE bodega_central.articulos_art_id_seq OWNER TO pgadmin;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: articulos_ingreso; Type: TABLE; Schema: bodega_central; Owner: pgadmin
--

CREATE TABLE bodega_central.articulos_ingreso (
    ari_id integer DEFAULT nextval('bodega_central.articulos_art_id_seq'::regclass) NOT NULL,
    ari_codigo integer,
    ari_nombre text,
    ari_cantidad double precision,
    ari_unidad_medida character varying(10),
    ari_espacio smallint NOT NULL,
    ari_sucursal_destino smallint,
    ari_ingreso integer
);


ALTER TABLE bodega_central.articulos_ingreso OWNER TO pgadmin;

--
-- Name: articulos_salida; Type: TABLE; Schema: bodega_central; Owner: pgadmin
--

CREATE TABLE bodega_central.articulos_salida (
    ars_id integer NOT NULL,
    ars_articulo_ingreso integer,
    ars_cantidad integer,
    ars_salida integer
);


ALTER TABLE bodega_central.articulos_salida OWNER TO pgadmin;

--
-- Name: articulos_salida_art_id_seq; Type: SEQUENCE; Schema: bodega_central; Owner: pgadmin
--

CREATE SEQUENCE bodega_central.articulos_salida_art_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE bodega_central.articulos_salida_art_id_seq OWNER TO pgadmin;

--
-- Name: articulos_salida_art_id_seq; Type: SEQUENCE OWNED BY; Schema: bodega_central; Owner: pgadmin
--

ALTER SEQUENCE bodega_central.articulos_salida_art_id_seq OWNED BY bodega_central.articulos_salida.ars_id;


--
-- Name: espacios; Type: TABLE; Schema: bodega_central; Owner: pgadmin
--

CREATE TABLE bodega_central.espacios (
    esp_id integer NOT NULL,
    esp_numero smallint NOT NULL,
    esp_seccion smallint NOT NULL,
    esp_estante smallint NOT NULL,
    esp_ancho smallint,
    esp_alto smallint,
    esp_fondo smallint,
    esp_peso smallint,
    esp_ocupado boolean DEFAULT false
);


ALTER TABLE bodega_central.espacios OWNER TO pgadmin;

--
-- Name: espacios_esp_id_seq1; Type: SEQUENCE; Schema: bodega_central; Owner: pgadmin
--

CREATE SEQUENCE bodega_central.espacios_esp_id_seq1
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE bodega_central.espacios_esp_id_seq1 OWNER TO pgadmin;

--
-- Name: espacios_esp_id_seq1; Type: SEQUENCE OWNED BY; Schema: bodega_central; Owner: pgadmin
--

ALTER SEQUENCE bodega_central.espacios_esp_id_seq1 OWNED BY bodega_central.espacios.esp_id;


--
-- Name: estantes; Type: TABLE; Schema: bodega_central; Owner: pgadmin
--

CREATE TABLE bodega_central.estantes (
    est_id integer NOT NULL,
    est_capacidad smallint,
    est_posicion smallint
);


ALTER TABLE bodega_central.estantes OWNER TO pgadmin;

--
-- Name: estantes_est_id_seq; Type: SEQUENCE; Schema: bodega_central; Owner: pgadmin
--

CREATE SEQUENCE bodega_central.estantes_est_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE bodega_central.estantes_est_id_seq OWNER TO pgadmin;

--
-- Name: estantes_est_id_seq; Type: SEQUENCE OWNED BY; Schema: bodega_central; Owner: pgadmin
--

ALTER SEQUENCE bodega_central.estantes_est_id_seq OWNED BY bodega_central.estantes.est_id;


--
-- Name: ingresos; Type: TABLE; Schema: bodega_central; Owner: pgadmin
--

CREATE TABLE bodega_central.ingresos (
    ing_id integer NOT NULL,
    ing_orden_compra integer,
    ing_tipo_documento smallint,
    ing_documento integer,
    ing_despacho_estimado date,
    ing_fecha_ingreso timestamp without time zone,
    ing_rut_transportista integer,
    ing_dv_transportista character varying(1),
    ing_orden_transporte character varying(16),
    ing_fecha_transporte date,
    ing_peso_transporte integer,
    ing_observacion text
);


ALTER TABLE bodega_central.ingresos OWNER TO pgadmin;

--
-- Name: salidas; Type: TABLE; Schema: bodega_central; Owner: pgadmin
--

CREATE TABLE bodega_central.salidas (
    sld_id integer NOT NULL,
    sld_fecha_salida timestamp without time zone,
    sld_bodega_destino smallint,
    sld_observacion text,
    sld_orden_traspaso integer
);


ALTER TABLE bodega_central.salidas OWNER TO pgadmin;

--
-- Name: secciones; Type: TABLE; Schema: bodega_central; Owner: pgadmin
--

CREATE TABLE bodega_central.secciones (
    sec_id integer NOT NULL,
    sec_estante smallint NOT NULL,
    sec_capacidad smallint
);


ALTER TABLE bodega_central.secciones OWNER TO pgadmin;

--
-- Name: seccion_sec_id_seq; Type: SEQUENCE; Schema: bodega_central; Owner: pgadmin
--

CREATE SEQUENCE bodega_central.seccion_sec_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE bodega_central.seccion_sec_id_seq OWNER TO pgadmin;

--
-- Name: seccion_sec_id_seq; Type: SEQUENCE OWNED BY; Schema: bodega_central; Owner: pgadmin
--

ALTER SEQUENCE bodega_central.seccion_sec_id_seq OWNED BY bodega_central.secciones.sec_id;


--
-- Name: sucursales; Type: TABLE; Schema: bodega_central; Owner: pgadmin
--

CREATE TABLE bodega_central.sucursales (
    suc_id smallint NOT NULL,
    suc_nombre character varying(30),
    suc_direccion character varying(60),
    suc_telefono character varying(15),
    suc_empresa smallint
);


ALTER TABLE bodega_central.sucursales OWNER TO pgadmin;

--
-- Name: articulos_salida ars_id; Type: DEFAULT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.articulos_salida ALTER COLUMN ars_id SET DEFAULT nextval('bodega_central.articulos_salida_art_id_seq'::regclass);


--
-- Name: espacios esp_id; Type: DEFAULT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.espacios ALTER COLUMN esp_id SET DEFAULT nextval('bodega_central.espacios_esp_id_seq1'::regclass);


--
-- Data for Name: articulos_ingreso; Type: TABLE DATA; Schema: bodega_central; Owner: pgadmin
--

COPY bodega_central.articulos_ingreso (ari_id, ari_codigo, ari_nombre, ari_cantidad, ari_unidad_medida, ari_espacio, ari_sucursal_destino, ari_ingreso) FROM stdin;
8	92730	Tapacanto pvc teca italia 22x0.40 masisa	50	MT.	446	4	10497200
9	92730	Tapacanto pvc teca italia 22x0.40 masisa	50	MT.	447	4	10497200
5	91757	Tapacanto pvc fogon 22x0.40	50	MT.	442	4	10497199
6	91757	Tapacanto pvc fogon 22x0.40	50	MT.	443	4	10497199
7	26306	Mdf pin nogal ceniza 1c 3x1830x2500	10	PLS	444	4	10497199
10	98970	TEJA ASFALTICA SUPREME ONYX NEGRO 3.1	129	PAQT	335	14	123456
11	8686	TABIGAL MONT. 38 X 38 X 6 X 0.5 X 3000	100	TIRA	336	14	123456
12	8673	METALCON U CANAL 103X30X0.85X6000	28	TIRA	337	14	123456
13	8673	METALCON U CANAL 103X30X0.85X6000	40	TIRA	345	14	123456
\.


--
-- Data for Name: articulos_salida; Type: TABLE DATA; Schema: bodega_central; Owner: pgadmin
--

COPY bodega_central.articulos_salida (ars_id, ars_articulo_ingreso, ars_cantidad, ars_salida) FROM stdin;
2	6	50	123
1	5	25	123
3	10	129	701066
4	11	50	701066
5	12	28	701066
\.


--
-- Data for Name: espacios; Type: TABLE DATA; Schema: bodega_central; Owner: pgadmin
--

COPY bodega_central.espacios (esp_id, esp_numero, esp_seccion, esp_estante, esp_ancho, esp_alto, esp_fondo, esp_peso, esp_ocupado) FROM stdin;
1	1	1	8	100	60	120	200	f
2	2	1	8	100	60	120	200	f
3	3	1	8	100	60	120	200	f
4	4	1	8	100	110	120	1200	f
5	5	1	8	100	110	120	1200	f
6	6	1	8	100	110	120	1200	f
7	1	2	8	100	60	120	200	f
8	2	2	8	100	60	120	200	f
9	3	2	8	100	60	120	200	f
10	4	2	8	100	110	120	1200	f
11	5	2	8	100	110	120	1200	f
12	6	2	8	100	110	120	1200	f
13	1	3	8	100	60	120	200	f
14	2	3	8	100	60	120	200	f
15	3	3	8	100	60	120	200	f
16	4	3	8	100	110	120	1200	f
17	5	3	8	100	110	120	1200	f
18	6	3	8	100	110	120	1200	f
19	1	4	8	100	60	120	200	f
20	2	4	8	100	60	120	200	f
21	3	4	8	100	60	120	200	f
22	4	4	8	100	110	120	1200	f
23	5	4	8	100	110	120	1200	f
24	6	4	8	100	110	120	1200	f
25	1	5	8	100	60	120	200	f
26	2	5	8	100	60	120	200	f
27	3	5	8	100	60	120	200	f
28	4	5	8	100	110	120	1200	f
29	5	5	8	100	110	120	1200	f
30	6	5	8	100	110	120	1200	f
31	1	6	8	100	60	120	200	f
32	2	6	8	100	60	120	200	f
33	3	6	8	100	60	120	200	f
34	4	6	8	100	110	120	1200	f
35	5	6	8	100	110	120	1200	f
36	6	6	8	100	110	120	1200	f
37	1	7	8	100	60	120	200	f
38	2	7	8	100	60	120	200	f
39	3	7	8	100	60	120	200	f
40	4	7	8	100	110	120	1200	f
41	5	7	8	100	110	120	1200	f
42	6	7	8	100	110	120	1200	f
43	1	8	8	100	60	120	200	f
44	2	8	8	100	60	120	200	f
45	3	8	8	100	60	120	200	f
46	4	8	8	100	110	120	1200	f
47	5	8	8	100	110	120	1200	f
48	6	8	8	100	110	120	1200	f
49	1	9	8	100	60	120	200	f
50	2	9	8	100	60	120	200	f
51	3	9	8	100	60	120	200	f
52	4	9	8	100	110	120	1200	f
53	5	9	8	100	110	120	1200	f
54	6	9	8	100	110	120	1200	f
55	1	10	8	100	60	120	200	f
56	2	10	8	100	60	120	200	f
57	3	10	8	100	60	120	200	f
58	4	10	8	100	110	120	1200	f
59	5	10	8	100	110	120	1200	f
60	6	10	8	100	110	120	1200	f
61	1	11	8	100	60	120	200	f
62	2	11	8	100	60	120	200	f
63	3	11	8	100	60	120	200	f
64	4	11	8	100	110	120	1200	f
65	5	11	8	100	110	120	1200	f
66	6	11	8	100	110	120	1200	f
67	1	12	8	100	60	120	200	f
68	2	12	8	100	60	120	200	f
69	3	12	8	100	60	120	200	f
70	4	12	8	100	110	120	1200	f
71	5	12	8	100	110	120	1200	f
72	6	12	8	100	110	120	1200	f
73	4	13	8	100	110	120	1200	f
74	5	13	8	100	110	120	1200	f
75	6	13	8	100	110	120	1200	f
76	1	14	8	100	60	120	200	f
77	2	14	8	100	60	120	200	f
78	3	14	8	100	60	120	200	f
79	4	14	8	100	110	120	1200	f
80	5	14	8	100	110	120	1200	f
81	6	14	8	100	110	120	1200	f
82	1	15	8	100	60	120	200	f
83	2	15	8	100	60	120	200	f
84	3	15	8	100	60	120	200	f
85	4	15	8	100	110	120	1200	f
86	5	15	8	100	110	120	1200	f
87	6	15	8	100	110	120	1200	f
88	1	16	8	100	60	120	200	f
89	2	16	8	100	60	120	200	f
90	3	16	8	100	60	120	200	f
91	4	16	8	100	110	120	1200	f
92	5	16	8	100	110	120	1200	f
93	6	16	8	100	110	120	1200	f
94	1	17	8	100	60	120	200	f
95	2	17	8	100	60	120	200	f
96	3	17	8	100	60	120	200	f
97	4	17	8	100	110	120	1200	f
98	5	17	8	100	110	120	1200	f
99	6	17	8	100	110	120	1200	f
100	1	18	8	100	60	120	200	f
101	2	18	8	100	60	120	200	f
102	3	18	8	100	60	120	200	f
103	4	18	8	100	110	120	1200	f
104	5	18	8	100	110	120	1200	f
105	6	18	8	100	110	120	1200	f
106	1	20	8	100	60	120	200	f
107	2	20	8	100	60	120	200	f
108	3	20	8	100	60	120	200	f
109	4	20	8	100	110	120	1200	f
110	5	20	8	100	110	120	1200	f
111	6	20	8	100	110	120	1200	f
112	1	21	8	100	60	120	200	f
113	2	21	8	100	60	120	200	f
114	3	21	8	100	60	120	200	f
115	4	21	8	100	110	120	1200	f
116	5	21	8	100	110	120	1200	f
117	6	21	8	100	110	120	1200	f
118	1	1	9	100	110	120	1200	f
119	2	1	9	100	110	120	1200	f
120	3	1	9	100	110	120	1200	f
121	4	1	9	100	110	120	1200	f
122	1	2	9	100	110	120	1200	f
123	2	2	9	100	110	120	1200	f
124	3	2	9	100	110	120	1200	f
125	4	2	9	100	110	120	1200	f
126	1	3	9	100	110	120	1200	f
127	2	3	9	100	110	120	1200	f
128	3	3	9	100	110	120	1200	f
129	4	3	9	100	110	120	1200	f
130	1	1	7	100	110	120	1200	f
131	2	1	7	100	110	120	1200	f
132	3	1	7	100	110	120	1200	f
133	4	1	7	100	110	120	1200	f
134	1	2	7	100	110	120	1200	f
135	2	2	7	100	110	120	1200	f
136	3	2	7	100	110	120	1200	f
137	4	2	7	100	110	120	1200	f
138	1	3	7	100	110	120	1200	f
139	2	3	7	100	110	120	1200	f
140	3	3	7	100	110	120	1200	f
141	4	3	7	100	110	120	1200	f
142	1	1	10	100	110	120	2000	f
143	2	1	10	100	110	120	2000	f
144	3	1	10	100	110	120	2000	f
145	4	1	10	100	110	120	2000	f
146	1	2	10	100	110	120	2000	f
147	2	2	10	100	110	120	2000	f
148	3	2	10	100	110	120	2000	f
149	4	2	10	100	110	120	2000	f
150	1	3	10	100	110	120	2000	f
151	2	3	10	100	110	120	2000	f
152	3	3	10	100	110	120	2000	f
153	4	3	10	100	110	120	2000	f
154	1	4	10	100	110	120	2000	f
155	2	4	10	100	110	120	2000	f
156	3	4	10	100	110	120	2000	f
157	4	4	10	100	110	120	2000	f
158	1	5	10	100	110	120	2000	f
159	2	5	10	100	110	120	2000	f
160	3	5	10	100	110	120	2000	f
161	4	5	10	100	110	120	2000	f
162	1	6	10	100	110	120	2000	f
163	2	6	10	100	110	120	2000	f
164	3	6	10	100	110	120	2000	f
165	4	6	10	100	110	120	2000	f
166	1	7	10	100	110	120	2000	f
167	2	7	10	100	110	120	2000	f
168	3	7	10	100	110	120	2000	f
169	4	7	10	100	110	120	2000	f
170	1	8	10	100	110	120	2000	f
171	2	8	10	100	110	120	2000	f
172	3	8	10	100	110	120	2000	f
173	4	8	10	100	110	120	2000	f
174	1	1	6	100	110	120	1200	f
175	2	1	6	100	110	120	1200	f
176	3	1	6	100	110	120	1200	f
177	4	1	6	100	110	120	1200	f
178	1	2	6	100	110	120	1200	f
179	2	2	6	100	110	120	1200	f
180	3	2	6	100	110	120	1200	f
181	4	2	6	100	110	120	1200	f
182	1	3	6	100	110	120	1200	f
183	2	3	6	100	110	120	1200	f
184	3	3	6	100	110	120	1200	f
185	4	3	6	100	110	120	1200	f
186	1	4	6	100	110	120	1200	f
187	2	4	6	100	110	120	1200	f
188	3	4	6	100	110	120	1200	f
189	4	4	6	100	110	120	1200	f
190	1	5	6	100	110	120	1200	f
191	2	5	6	100	110	120	1200	f
192	3	5	6	100	110	120	1200	f
193	4	5	6	100	110	120	1200	f
194	1	6	6	100	110	120	1200	f
195	2	6	6	100	110	120	1200	f
196	3	6	6	100	110	120	1200	f
197	4	6	6	100	110	120	1200	f
198	1	7	6	100	110	120	1200	f
199	2	7	6	100	110	120	1200	f
200	3	7	6	100	110	120	1200	f
201	4	7	6	100	110	120	1200	f
202	1	8	6	100	110	120	1200	f
203	2	8	6	100	110	120	1200	f
204	3	8	6	100	110	120	1200	f
205	4	8	6	100	110	120	1200	f
206	1	9	6	100	110	120	1200	f
207	2	9	6	100	110	120	1200	f
208	3	9	6	100	110	120	1200	f
209	4	9	6	100	110	120	1200	f
210	1	1	11	100	110	120	2000	f
211	2	1	11	100	110	120	2000	f
212	3	1	11	100	110	120	2000	f
213	4	1	11	100	110	120	2000	f
214	1	2	11	100	110	120	2000	f
215	2	2	11	100	110	120	2000	f
216	3	2	11	100	110	120	2000	f
217	4	2	11	100	110	120	2000	f
218	1	3	11	100	110	120	2000	f
219	2	3	11	100	110	120	2000	f
220	3	3	11	100	110	120	2000	f
221	4	3	11	100	110	120	2000	f
222	1	4	11	100	110	120	2000	f
223	2	4	11	100	110	120	2000	f
224	3	4	11	100	110	120	2000	f
225	4	4	11	100	110	120	2000	f
226	1	5	11	100	110	120	2000	f
227	2	5	11	100	110	120	2000	f
228	3	5	11	100	110	120	2000	f
229	4	5	11	100	110	120	2000	f
230	1	6	11	100	110	120	2000	f
231	2	6	11	100	110	120	2000	f
232	3	6	11	100	110	120	2000	f
233	4	6	11	100	110	120	2000	f
234	1	7	11	100	110	120	2000	f
235	2	7	11	100	110	120	2000	f
236	3	7	11	100	110	120	2000	f
237	4	7	11	100	110	120	2000	f
238	1	8	11	100	110	120	2000	f
239	2	8	11	100	110	120	2000	f
240	3	8	11	100	110	120	2000	f
241	4	8	11	100	110	120	2000	f
242	1	1	5	100	110	120	1200	f
243	2	1	5	100	110	120	1200	f
244	3	1	5	100	110	120	1200	f
245	4	1	5	100	110	120	1200	f
246	1	2	5	100	110	120	1200	f
247	2	2	5	100	110	120	1200	f
248	3	2	5	100	110	120	1200	f
249	4	2	5	100	110	120	1200	f
250	1	3	5	100	110	120	1200	f
251	2	3	5	100	110	120	1200	f
252	3	3	5	100	110	120	1200	f
253	4	3	5	100	110	120	1200	f
254	1	4	5	100	110	120	1200	f
255	2	4	5	100	110	120	1200	f
256	3	4	5	100	110	120	1200	f
257	4	4	5	100	110	120	1200	f
258	1	5	5	100	110	120	1200	f
259	2	5	5	100	110	120	1200	f
260	3	5	5	100	110	120	1200	f
261	4	5	5	100	110	120	1200	f
262	1	6	5	100	110	120	1200	f
263	2	6	5	100	110	120	1200	f
264	3	6	5	100	110	120	1200	f
265	4	6	5	100	110	120	1200	f
266	1	7	5	100	110	120	1200	f
267	2	7	5	100	110	120	1200	f
268	3	7	5	100	110	120	1200	f
269	4	7	5	100	110	120	1200	f
270	1	8	5	100	110	120	1200	f
271	2	8	5	100	110	120	1200	f
272	3	8	5	100	110	120	1200	f
273	4	8	5	100	110	120	1200	f
274	1	9	5	100	110	120	1200	f
275	2	9	5	100	110	120	1200	f
276	3	9	5	100	110	120	1200	f
277	4	9	5	100	110	120	1200	f
278	1	1	12	100	110	120	1200	f
279	2	1	12	100	110	120	1200	f
280	3	1	12	100	110	120	1200	f
281	4	1	12	100	110	120	1200	f
282	1	2	12	100	110	120	1200	f
283	2	2	12	100	110	120	1200	f
284	3	2	12	100	110	120	1200	f
285	4	2	12	100	110	120	1200	f
286	1	3	12	100	110	120	1200	f
287	2	3	12	100	110	120	1200	f
288	3	3	12	100	110	120	1200	f
289	4	3	12	100	110	120	1200	f
290	1	1	4	100	110	120	1200	f
291	2	1	4	100	110	120	1200	f
292	3	1	4	100	110	120	1200	f
293	4	1	4	100	110	120	1200	f
294	1	2	4	100	110	120	1200	f
295	2	2	4	100	110	120	1200	f
296	3	2	4	100	110	120	1200	f
297	4	2	4	100	110	120	1200	f
298	1	3	4	100	110	120	1200	f
299	2	3	4	100	110	120	1200	f
300	3	3	4	100	110	120	1200	f
301	4	3	4	100	110	120	1200	f
302	1	1	13	100	110	120	1200	f
303	2	1	13	100	110	120	1200	f
304	3	1	13	100	110	120	1200	f
305	4	1	13	100	110	120	1200	f
306	1	2	13	100	110	120	1200	f
307	2	2	13	100	110	120	1200	f
308	3	2	13	100	110	120	1200	f
309	4	2	13	100	110	120	1200	f
310	1	3	13	100	110	120	1200	f
311	2	3	13	100	110	120	1200	f
312	3	3	13	100	110	120	1200	f
313	4	3	13	100	110	120	1200	f
314	1	4	13	100	110	120	1200	f
315	2	4	13	100	110	120	1200	f
316	3	4	13	100	110	120	1200	f
317	4	4	13	100	110	120	1200	f
318	1	5	13	100	110	120	1200	f
319	2	5	13	100	110	120	1200	f
320	3	5	13	100	110	120	1200	f
321	4	5	13	100	110	120	1200	f
322	1	6	13	100	110	120	1200	f
323	2	6	13	100	110	120	1200	f
324	3	6	13	100	110	120	1200	f
325	4	6	13	100	110	120	1200	f
326	1	7	13	100	110	120	1200	f
327	2	7	13	100	110	120	1200	f
328	3	7	13	100	110	120	1200	f
329	4	7	13	100	110	120	1200	f
330	1	8	13	100	110	120	1200	f
331	2	8	13	100	110	120	1200	f
332	3	8	13	100	110	120	1200	f
333	4	8	13	100	110	120	1200	f
334	1	1	3	100	110	120	1200	f
338	1	2	3	100	110	120	1200	f
339	2	2	3	100	110	120	1200	f
340	3	2	3	100	110	120	1200	f
341	4	2	3	100	110	120	1200	f
342	1	3	3	100	110	120	1200	f
343	2	3	3	100	110	120	1200	f
344	3	3	3	100	110	120	1200	f
346	1	4	3	100	110	120	1200	f
347	2	4	3	100	110	120	1200	f
348	3	4	3	100	110	120	1200	f
349	4	4	3	100	110	120	1200	f
350	1	5	3	100	110	120	1200	f
351	2	5	3	100	110	120	1200	f
352	3	5	3	100	110	120	1200	f
353	4	5	3	100	110	120	1200	f
354	1	6	3	100	110	120	1200	f
355	2	6	3	100	110	120	1200	f
356	3	6	3	100	110	120	1200	f
357	4	6	3	100	110	120	1200	f
358	1	7	3	100	110	120	1200	f
359	2	7	3	100	110	120	1200	f
360	3	7	3	100	110	120	1200	f
361	4	7	3	100	110	120	1200	f
362	1	8	3	100	110	120	1200	f
363	2	8	3	100	110	120	1200	f
364	3	8	3	100	110	120	1200	f
365	4	8	3	100	110	120	1200	f
366	1	9	3	100	110	120	1200	f
367	2	9	3	100	110	120	1200	f
368	3	9	3	100	110	120	1200	f
369	4	9	3	100	110	120	1200	f
370	1	1	14	100	110	120	2000	f
371	2	1	14	100	110	120	2000	f
372	3	1	14	100	110	120	2000	f
373	4	1	14	100	110	120	2000	f
374	1	2	14	100	110	120	2000	f
375	2	2	14	100	110	120	2000	f
376	3	2	14	100	110	120	2000	f
377	4	2	14	100	110	120	2000	f
378	1	3	14	100	110	120	2000	f
379	2	3	14	100	110	120	2000	f
380	3	3	14	100	110	120	2000	f
381	4	3	14	100	110	120	2000	f
382	1	4	14	100	110	120	2000	f
383	2	4	14	100	110	120	2000	f
384	3	4	14	100	110	120	2000	f
385	4	4	14	100	110	120	2000	f
386	1	5	14	100	110	120	2000	f
387	2	5	14	100	110	120	2000	f
388	3	5	14	100	110	120	2000	f
389	4	5	14	100	110	120	2000	f
390	1	6	14	100	110	120	2000	f
391	2	6	14	100	110	120	2000	f
392	3	6	14	100	110	120	2000	f
393	4	6	14	100	110	120	2000	f
394	1	7	14	100	110	120	2000	f
395	2	7	14	100	110	120	2000	f
396	3	7	14	100	110	120	2000	f
397	4	7	14	100	110	120	2000	f
398	1	8	14	100	110	120	2000	f
399	2	8	14	100	110	120	2000	f
400	3	8	14	100	110	120	2000	f
401	4	8	14	100	110	120	2000	f
402	1	1	2	100	110	120	1200	f
403	2	1	2	100	110	120	1200	f
404	3	1	2	100	110	120	1200	f
405	4	1	2	100	110	120	1200	f
406	1	2	2	100	110	120	1200	f
407	2	2	2	100	110	120	1200	f
408	3	2	2	100	110	120	1200	f
409	4	2	2	100	110	120	1200	f
410	1	3	2	100	110	120	1200	f
411	2	3	2	100	110	120	1200	f
412	3	3	2	100	110	120	1200	f
413	4	3	2	100	110	120	1200	f
414	1	4	2	100	110	120	1200	f
415	2	4	2	100	110	120	1200	f
416	3	4	2	100	110	120	1200	f
417	4	4	2	100	110	120	1200	f
418	1	5	2	100	110	120	1200	f
419	2	5	2	100	110	120	1200	f
420	3	5	2	100	110	120	1200	f
421	4	5	2	100	110	120	1200	f
422	1	6	2	100	110	120	1200	f
423	2	6	2	100	110	120	1200	f
424	3	6	2	100	110	120	1200	f
425	4	6	2	100	110	120	1200	f
426	1	7	2	100	110	120	1200	f
427	2	7	2	100	110	120	1200	f
428	3	7	2	100	110	120	1200	f
429	4	7	2	100	110	120	1200	f
430	1	1	15	100	110	120	2000	f
431	2	1	15	100	110	120	2000	f
432	3	1	15	100	110	120	2000	f
433	4	1	15	100	110	120	2000	f
434	1	2	15	100	110	120	2000	f
435	2	2	15	100	110	120	2000	f
436	3	2	15	100	110	120	2000	f
437	4	2	15	100	110	120	2000	f
438	1	3	15	100	110	120	2000	f
439	2	3	15	100	110	120	2000	f
440	3	3	15	100	110	120	2000	f
441	4	3	15	100	110	120	2000	f
445	4	1	1	100	110	120	1200	f
448	3	2	1	100	110	120	1200	f
449	4	2	1	100	110	120	1200	f
450	1	3	1	100	110	120	1200	f
451	2	3	1	100	110	120	1200	f
452	3	3	1	100	110	120	1200	f
453	4	3	1	100	110	120	1200	f
454	1	4	1	100	110	120	1200	f
455	2	4	1	100	110	120	1200	f
456	3	4	1	100	110	120	1200	f
457	4	4	1	100	110	120	1200	f
458	1	5	1	100	110	120	1200	f
459	2	5	1	100	110	120	1200	f
460	3	5	1	100	110	120	1200	f
461	4	5	1	100	110	120	1200	f
462	1	6	1	100	110	120	1200	f
463	2	6	1	100	110	120	1200	f
464	3	6	1	100	110	120	1200	f
465	4	6	1	100	110	120	1200	f
466	1	7	1	100	110	120	1200	f
467	2	7	1	100	110	120	1200	f
468	3	7	1	100	110	120	1200	f
469	4	7	1	100	110	120	1200	f
470	1	1	16	100	110	120	2000	f
471	2	1	16	100	110	120	2000	f
444	3	1	1	100	110	120	1200	t
446	1	2	1	100	110	120	1200	t
335	2	1	3	100	110	120	1200	t
336	3	1	3	100	110	120	1200	t
337	4	1	3	100	110	120	1200	t
345	4	3	3	100	110	120	1200	t
472	3	1	16	100	110	120	2000	f
473	4	1	16	100	110	120	2000	f
474	1	2	16	100	110	120	2000	f
475	2	2	16	100	110	120	2000	f
476	3	2	16	100	110	120	2000	f
477	4	2	16	100	110	120	2000	f
478	1	3	16	100	110	120	2000	f
479	2	3	16	100	110	120	2000	f
480	3	3	16	100	110	120	2000	f
481	4	3	16	100	110	120	2000	f
482	1	4	16	100	110	120	2000	f
483	2	4	16	100	110	120	2000	f
484	3	4	16	100	110	120	2000	f
485	4	4	16	100	110	120	2000	f
486	1	5	16	100	110	120	2000	f
487	2	5	16	100	110	120	2000	f
488	3	5	16	100	110	120	2000	f
489	4	5	16	100	110	120	2000	f
490	1	6	16	100	110	120	2000	f
491	2	6	16	100	110	120	2000	f
492	3	6	16	100	110	120	2000	f
493	4	6	16	100	110	120	2000	f
494	1	7	16	100	110	120	2000	f
495	2	7	16	100	110	120	2000	f
496	3	7	16	100	110	120	2000	f
497	4	7	16	100	110	120	2000	f
498	1	8	16	100	110	120	2000	f
499	2	8	16	100	110	120	2000	f
500	3	8	16	100	110	120	2000	f
501	4	8	16	100	110	120	2000	f
502	1	1	17	100	110	120	2000	f
503	2	1	17	100	110	120	2000	f
504	3	1	17	100	110	120	2000	f
505	4	1	17	100	110	120	2000	f
506	1	2	17	100	110	120	2000	f
507	2	2	17	100	110	120	2000	f
508	3	2	17	100	110	120	2000	f
509	4	2	17	100	110	120	2000	f
510	1	3	17	100	110	120	2000	f
511	2	3	17	100	110	120	2000	f
512	3	3	17	100	110	120	2000	f
513	4	3	17	100	110	120	2000	f
514	1	4	17	100	110	120	2000	f
515	2	4	17	100	110	120	2000	f
516	3	4	17	100	110	120	2000	f
517	4	4	17	100	110	120	2000	f
518	1	5	17	100	110	120	2000	f
519	2	5	17	100	110	120	2000	f
520	3	5	17	100	110	120	2000	f
521	4	5	17	100	110	120	2000	f
522	1	6	17	100	110	120	2000	f
523	2	6	17	100	110	120	2000	f
524	3	6	17	100	110	120	2000	f
525	4	6	17	100	110	120	2000	f
526	1	7	17	100	110	120	2000	f
527	2	7	17	100	110	120	2000	f
528	3	7	17	100	110	120	2000	f
529	4	7	17	100	110	120	2000	f
530	1	8	17	100	110	120	2000	f
531	2	8	17	100	110	120	2000	f
532	3	8	17	100	110	120	2000	f
533	4	8	17	100	110	120	2000	f
534	1	1	18	100	110	120	2000	f
535	2	1	18	100	110	120	2000	f
536	3	1	18	100	110	120	2000	f
537	4	1	18	100	110	120	2000	f
538	1	2	18	100	110	120	2000	f
539	2	2	18	100	110	120	2000	f
540	3	2	18	100	110	120	2000	f
541	4	2	18	100	110	120	2000	f
542	1	3	18	100	110	120	2000	f
543	2	3	18	100	110	120	2000	f
544	3	3	18	100	110	120	2000	f
545	4	3	18	100	110	120	2000	f
546	1	1	19	100	110	120	2000	f
547	2	1	19	100	110	120	2000	f
548	3	1	19	100	110	120	2000	f
549	4	1	19	100	110	120	2000	f
550	1	2	19	100	110	120	2000	f
551	2	2	19	100	110	120	2000	f
552	3	2	19	100	110	120	2000	f
553	4	2	19	100	110	120	2000	f
554	1	3	19	100	110	120	2000	f
555	2	3	19	100	110	120	2000	f
556	3	3	19	100	110	120	2000	f
557	4	3	19	100	110	120	2000	f
558	1	4	19	100	110	120	2000	f
559	2	4	19	100	110	120	2000	f
560	3	4	19	100	110	120	2000	f
561	4	4	19	100	110	120	2000	f
562	1	5	19	100	110	120	2000	f
563	2	5	19	100	110	120	2000	f
564	3	5	19	100	110	120	2000	f
565	4	5	19	100	110	120	2000	f
566	1	6	19	100	110	120	2000	f
567	2	6	19	100	110	120	2000	f
568	3	6	19	100	110	120	2000	f
569	4	6	19	100	110	120	2000	f
570	1	7	19	100	110	120	2000	f
571	2	7	19	100	110	120	2000	f
572	3	7	19	100	110	120	2000	f
573	4	7	19	100	110	120	2000	f
574	1	8	19	100	110	120	2000	f
575	2	8	19	100	110	120	2000	f
576	3	8	19	100	110	120	2000	f
577	4	8	19	100	110	120	2000	f
442	1	1	1	100	110	120	1200	t
443	2	1	1	100	110	120	1200	t
447	2	2	1	100	110	120	1200	t
578	1	1	20	500	600	1200	10000	f
579	1	1	21	500	600	1200	10000	f
580	1	1	22	500	600	1200	10000	f
581	1	1	23	500	600	1200	10000	f
582	1	1	24	500	600	1200	10000	f
\.


--
-- Data for Name: estantes; Type: TABLE DATA; Schema: bodega_central; Owner: pgadmin
--

COPY bodega_central.estantes (est_id, est_capacidad, est_posicion) FROM stdin;
8	21	1
1	7	15
2	7	13
3	9	11
4	3	9
5	9	7
6	9	5
7	3	3
9	3	2
10	8	4
11	8	6
12	3	8
13	8	10
14	8	12
15	3	14
16	8	16
17	8	18
18	3	20
19	8	22
20	1	23
21	1	24
22	1	25
23	1	26
24	1	27
\.


--
-- Data for Name: ingresos; Type: TABLE DATA; Schema: bodega_central; Owner: pgadmin
--

COPY bodega_central.ingresos (ing_id, ing_orden_compra, ing_tipo_documento, ing_documento, ing_despacho_estimado, ing_fecha_ingreso, ing_rut_transportista, ing_dv_transportista, ing_orden_transporte, ing_fecha_transporte, ing_peso_transporte, ing_observacion) FROM stdin;
10497199	1133762	33	123	2024-07-25	2024-07-17 12:43:05	0	0	123456	2024-07-17	1500	texto ingreso
10497200	1133762	52	5678	2024-07-26	2024-07-19 15:28:53	55555555	5	123456	2024-07-19	50	comentario de prueba
123456	654321	33	534	2024-08-20	2024-07-17 12:43:05	\N	\N	2342	2024-07-17	2000	\N
\.


--
-- Data for Name: salidas; Type: TABLE DATA; Schema: bodega_central; Owner: pgadmin
--

COPY bodega_central.salidas (sld_id, sld_fecha_salida, sld_bodega_destino, sld_observacion, sld_orden_traspaso) FROM stdin;
701066	2024-08-22 12:17:01	14	texto de prueba en salida 1	31924
123	2024-08-13 12:00:00	14	observacion de salida	123
\.


--
-- Data for Name: secciones; Type: TABLE DATA; Schema: bodega_central; Owner: pgadmin
--

COPY bodega_central.secciones (sec_id, sec_estante, sec_capacidad) FROM stdin;
1	1	4
2	1	4
3	1	4
4	1	4
5	1	4
6	1	4
7	1	4
1	2	4
2	2	4
3	2	4
4	2	4
5	2	4
6	2	4
7	2	4
1	3	4
2	3	4
3	3	4
4	3	4
5	3	4
6	3	4
7	3	4
8	3	4
9	3	4
3	4	4
2	4	4
1	4	4
1	5	4
2	5	4
3	5	4
4	5	4
5	5	4
6	5	4
7	5	4
8	5	4
9	5	4
1	6	4
2	6	4
3	6	4
4	6	4
5	6	4
6	6	4
7	6	4
8	6	4
9	6	4
3	7	4
2	7	4
1	7	4
3	9	4
2	9	4
1	9	4
1	10	4
2	10	4
3	10	4
4	10	4
5	10	4
6	10	4
7	10	4
8	10	4
1	11	4
2	11	4
3	11	4
4	11	4
5	11	4
6	11	4
7	11	4
8	11	4
1	12	4
2	12	4
3	12	4
1	13	4
2	13	4
3	13	4
4	13	4
5	13	4
6	13	4
7	13	4
8	13	4
1	14	4
2	14	4
3	14	4
4	14	4
5	14	4
6	14	4
7	14	4
8	14	4
1	15	4
2	15	4
3	15	4
1	16	4
2	16	4
3	16	4
4	16	4
5	16	4
6	16	4
7	16	4
8	16	4
1	17	4
2	17	4
3	17	4
4	17	4
5	17	4
6	17	4
7	17	4
8	17	4
1	19	4
2	19	4
3	19	4
4	19	4
5	19	4
6	19	4
7	19	4
8	19	4
1	18	4
2	18	4
3	18	4
1	8	6
2	8	6
3	8	6
4	8	6
5	8	6
6	8	6
7	8	6
8	8	6
9	8	6
10	8	6
11	8	6
12	8	6
13	8	6
14	8	6
15	8	6
16	8	6
17	8	6
18	8	6
20	8	6
21	8	6
1	20	1
1	21	1
1	22	1
1	23	1
1	24	1
\.


--
-- Data for Name: sucursales; Type: TABLE DATA; Schema: bodega_central; Owner: pgadmin
--

COPY bodega_central.sucursales (suc_id, suc_nombre, suc_direccion, suc_telefono, suc_empresa) FROM stdin;
0	ADQUISICIONES	GENERAL DEL CANTO #487	22 2362340	2
1	ECOM.VALDIVIA	AVDA. RAMON PICARTE #688, VALDIVIA	63 2347272	2
2	ECOM.TEMUCO	PORTALES #1196, TEMUCO	45 2230800	2
3	MCT.VALDIVIA	AVDA. RAMON PICARTE #3981, VALDIVIA	63 2347373	2
4	MCT.TEMUCO	CAUPOLICAN #957, TEMUCO	45 2210095	2
5	ECOM.SANTIAGO	NATANIEL COX #1065, SANTIAGO	22 6354002	2
6	ECOMCT.LOSANG	AVENIDA ALEMANIA #1120, LOS ANGELES	43 2318538	2
8	PLC.VALDIVIA	AVDA.PICARTE #2669, VALDIVIA	63 2347575	2
9	MCT.RENCA	MIRAFLORES #8740, RENCA	22 6274110	2
11	ECOMCT.PMONTT	AVDA.CARDONAL #2521, PUERTO MONTT	65 2310260	2
12	MCT.CASTRO	PEDRO MONTT #1302, CASTRO	65 2638899	2
13	ECOM.CONCEPCION	MANUEL RODRIGUEZ #551, CONCEPCION	41 2244077	2
14	ECOMCT.OSORNO	AVDA. ALCALDE RENE SORIANO BORQUEZ #2640, OSORNO	64 2207880	2
15	MCT.CONCEPCION	V/NUNEZ DE BALBOA #9067, CONCEPCION	41 3161700	2
16	ECOM.CASTRO	PANAMERICANA NORTE #1202, CASTRO	65 2636467	2
17	BODEGA TEMUCO	RUDECINDO ORTEGA #01850, TEMUCO	45 2211000	2
18	MCT VILLARRICA	SATURNINO EPULEF #1276, VILLARRICA	45 2599588	2
101	INVERLUZ	PATRICIO LYNCH #790, TEMUCO		1
\.


--
-- Name: articulos_art_id_seq; Type: SEQUENCE SET; Schema: bodega_central; Owner: pgadmin
--

SELECT pg_catalog.setval('bodega_central.articulos_art_id_seq', 15, true);


--
-- Name: articulos_salida_art_id_seq; Type: SEQUENCE SET; Schema: bodega_central; Owner: pgadmin
--

SELECT pg_catalog.setval('bodega_central.articulos_salida_art_id_seq', 5, true);


--
-- Name: espacios_esp_id_seq1; Type: SEQUENCE SET; Schema: bodega_central; Owner: pgadmin
--

SELECT pg_catalog.setval('bodega_central.espacios_esp_id_seq1', 578, true);


--
-- Name: estantes_est_id_seq; Type: SEQUENCE SET; Schema: bodega_central; Owner: pgadmin
--

SELECT pg_catalog.setval('bodega_central.estantes_est_id_seq', 6, true);


--
-- Name: seccion_sec_id_seq; Type: SEQUENCE SET; Schema: bodega_central; Owner: pgadmin
--

SELECT pg_catalog.setval('bodega_central.seccion_sec_id_seq', 1, false);


--
-- Name: articulos_ingreso articulos_pkey; Type: CONSTRAINT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.articulos_ingreso
    ADD CONSTRAINT articulos_pkey PRIMARY KEY (ari_id);


--
-- Name: articulos_salida articulos_salida_pkey; Type: CONSTRAINT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.articulos_salida
    ADD CONSTRAINT articulos_salida_pkey PRIMARY KEY (ars_id);


--
-- Name: espacios espacios_pk; Type: CONSTRAINT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.espacios
    ADD CONSTRAINT espacios_pk PRIMARY KEY (esp_id);


--
-- Name: estantes estantes_pkey; Type: CONSTRAINT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.estantes
    ADD CONSTRAINT estantes_pkey PRIMARY KEY (est_id);


--
-- Name: ingresos ingresos_pkey; Type: CONSTRAINT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.ingresos
    ADD CONSTRAINT ingresos_pkey PRIMARY KEY (ing_id);


--
-- Name: salidas salidas_pkey; Type: CONSTRAINT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.salidas
    ADD CONSTRAINT salidas_pkey PRIMARY KEY (sld_id);


--
-- Name: secciones seccion_pk; Type: CONSTRAINT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.secciones
    ADD CONSTRAINT seccion_pk PRIMARY KEY (sec_id, sec_estante);


--
-- Name: sucursales sucursales_pkey; Type: CONSTRAINT; Schema: bodega_central; Owner: pgadmin
--

ALTER TABLE ONLY bodega_central.sucursales
    ADD CONSTRAINT sucursales_pkey PRIMARY KEY (suc_id);


--
-- PostgreSQL database dump complete
--

