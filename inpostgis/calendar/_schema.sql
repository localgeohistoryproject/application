--
-- CREATE SCHEMA WITH PERMISSIONS
--

CREATE SCHEMA calendar;
GRANT USAGE ON SCHEMA calendar TO readonly;

--
-- CREATE TABLES
--

CREATE TABLE calendar.hebrewmonth (
    k integer NOT NULL,
    m integer NOT NULL,
    a integer NOT NULL
);

COMMENT ON TABLE calendar.hebrewmonth IS 'Derived from: Richards, E. G. "Calendars." In Explanatory Supplement to the Astronomical Almanac,
  3rd ed., edited by Sean E. Urban and P. Kenneth Seidelmann, 585-624. Mill Valley, Calif.:
  University Science Books, 2012. https://aa.usno.navy.mil/downloads/c15_usb_online.pdf
Source: Table 15.15
Note: Mathematical principles, formulas, algorithms, or equations are not copyrightable. See
  U.S. COPYRIGHT OFFICE, COMPENDIUM OF U.S. COPYRIGHT OFFICE PRACTICES § 313.3(A) (3d ed. 2021).';

CREATE TABLE calendar.locale
(
    localeid character varying(2) NOT NULL,
    dayfirst boolean NOT NULL,
    daysuffix text NOT NULL,
    daydelimiter text NOT NULL,
    monthdelimiter text NOT NULL,
    dayonesuffix text NOT NULL
);

CREATE TABLE calendar.monthday (
    type "char" NOT NULL,
    monthinteger integer NOT NULL,
    monthday integer NOT NULL
);

CREATE TABLE calendar.monthshort (
    type "char" NOT NULL,
    monthinteger integer NOT NULL,
    monthshort text NOT NULL
);

CREATE TABLE calendar.monthlong (
    type "char" NOT NULL,
    locale character varying(2) NOT NULL,
    monthinteger integer NOT NULL,
    monthlong text NOT NULL,
    monthabbreviation text NOT NULL
);

CREATE TABLE calendar.part (
    partid text NOT NULL,
    type "char",
    monthshorttype "char",
    monthlongtype "char",
    monthdaytype "char",
    monthmax integer,
    monthstart integer,
    daystart integer,
    yearbefore integer,
    yearafter integer,
    monthdifference integer
);

COMMENT ON COLUMN calendar.part.type IS 'This determines the calendar conversion type';

COMMENT ON COLUMN calendar.part.monthshorttype IS 'This determines how the text and integer versions of the months relate';

COMMENT ON COLUMN calendar.part.monthlongtype IS 'This determines the names of the months in their locales';

COMMENT ON COLUMN calendar.part.monthdaytype IS 'This determines what days are valid in which months';

CREATE TABLE calendar.qualifier (
    qualifierid text NOT NULL,
    qualifierabbreviation text NOT NULL,
    qualifiershort text NOT NULL,
    qualifierisinstant boolean NOT NULL
);

CREATE TABLE calendar.qualifierlocale (
    qualifier text NOT NULL,
    locale text NOT NULL,
    qualifiershort text NOT NULL,
    qualifierlong text NOT NULL
);

CREATE TABLE calendar.regnalyear
(
    monarch text NOT NULL,
    regnalyearnumber integer NOT NULL,
    regnalyeardaterangetext text NOT NULL
);

CREATE TABLE calendar.type (
    typeid "char" NOT NULL,
    typelong text NOT NULL,
    typegroup text NOT NULL,
    y integer,
    j integer,
    m integer,
    n integer,
    r integer,
    p integer,
    q integer,
    v integer,
    u integer,
    s integer,
    t integer,
    w integer,
    a integer,
    b integer,
    c integer
);

COMMENT ON TABLE calendar.type IS 'Derived from: Richards, E. G. "Calendars." In Explanatory Supplement to the Astronomical Almanac,
  3rd ed., edited by Sean E. Urban and P. Kenneth Seidelmann, 585-624. Mill Valley, Calif.:
  University Science Books, 2012. https://aa.usno.navy.mil/downloads/c15_usb_online.pdf
Source: Table 15.14
Note: Mathematical principles, formulas, algorithms, or equations are not copyrightable. See
  U.S. COPYRIGHT OFFICE, COMPENDIUM OF U.S. COPYRIGHT OFFICE PRACTICES § 313.3(A) (3d ed. 2021).';

--
-- CREATE SIMPLE TYPES
--

CREATE TYPE calendar.historicdate AS (
	gregorian date,
	"precision" text,
	qualifier text,
	yeardouble text,
	calendar text
);

CREATE TYPE calendar.yearmonthday AS (
	year integer,
	month integer,
	day integer
);

--
-- CREATE RANGE TYPE FUNCTIONS
--

CREATE FUNCTION calendar.date(historicdate calendar.historicdate) RETURNS date
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    BEGIN

        RETURN historicdate.gregorian;

    END;

$$;

CREATE FUNCTION calendar.historicdate_difference(historicdate1 calendar.historicdate, historicdate2 calendar.historicdate) RETURNS double precision
    LANGUAGE sql IMMUTABLE SECURITY DEFINER
    AS $$
    SELECT cast(
      CASE
        WHEN historicdate1 IS NULL AND historicdate2 IS NULL THEN 0
        ELSE coalesce(calendar.date(historicdate1), '4000-01-01 BC'::date) -
          coalesce(calendar.date(historicdate2), '4000-01-01'::date)
      END
    as float);
$$;

--
-- CREATE RANGE TYPES
--

CREATE TYPE calendar.historicdaterange AS RANGE (
    subtype = calendar.historicdate,
    multirange_type_name = calendar.historicdatemultirange,
    subtype_diff = calendar.historicdate_difference
);

--
-- CREATE PREREQUISITE FUNCTIONS
--

CREATE FUNCTION calendar.hebrewfirst(year integer) RETURNS integer
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        Derived from: Richards, E. G. "Calendars." In Explanatory Supplement to the Astronomical Almanac,
          3rd ed., edited by Sean E. Urban and P. Kenneth Seidelmann, 585-624. Mill Valley, Calif.:
          University Science Books, 2012. https://aa.usno.navy.mil/downloads/c15_usb_online.pdf
        Note: Mathematical principles, formulas, algorithms, or equations are not copyrightable. See
          U.S. COPYRIGHT OFFICE, COMPENDIUM OF U.S. COPYRIGHT OFFICE PRACTICES § 313.3(A) (3d ed. 2021).
    */

    DECLARE

      a bigint;
      b bigint;
      c bigint;
      d bigint;
      e bigint;
      f bigint;
      g bigint;
      h bigint;

    BEGIN

      /* 15.11.4 Algorithm 5 */

      a := (235 * year - 234)/19;
      b := 204 + 793 * a;
      c := 5 + 12 * a + b/1080;
      d := 1 + 29 * a + c/24;
      e := mod(b, 1080) + 1080 * mod(c, 24);
      f := 1 + mod(d, 7);
      g := mod(7 * year + 13, 19)/12;
      h := mod(7 * year + 6, 19)/12;

      IF e >= 19440 OR (e >= 9924 AND f = 3 AND g = 0) OR (e >= 16788 AND f = 2 AND g = 0 AND h = 1) THEN
        d := d + 1;
      END IF;

      RETURN d + mod(mod(d + 5, 7), 2) + 347997;

    END;

$$;

CREATE FUNCTION calendar.yearmonthday(gregorian date, typeidchar "char") RETURNS calendar.yearmonthday
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        Derived from: Richards, E. G. "Calendars." In Explanatory Supplement to the Astronomical Almanac,
          3rd ed., edited by Sean E. Urban and P. Kenneth Seidelmann, 585-624. Mill Valley, Calif.:
          University Science Books, 2012. https://aa.usno.navy.mil/downloads/c15_usb_online.pdf
        Also See: Errata in The Explanatory Supplement to the Astronomical Almanac (3rd edition, 1st printing).
          1 June 2020. https://aa.usno.navy.mil/downloads/exp_supp_errata.pdf
        Note: Mathematical principles, formulas, algorithms, or equations are not copyrightable. See
          U.S. COPYRIGHT OFFICE, COMPENDIUM OF U.S. COPYRIGHT OFFICE PRACTICES § 313.3(A) (3d ed. 2021).
    */

    DECLARE

      hebrewmonth calendar.hebrewmonth;
      type calendar.type;
      yearmonthday calendar.yearmonthday;

      a bigint;
      b bigint;
      c bigint;
      e bigint;
      f bigint;
      g bigint;
      gregorianj integer;
      h bigint;
      k1 bigint;
      m bigint;
      x bigint;
      z bigint;

    BEGIN

      -- Skip processing for empty values

      IF gregorian IS NULL THEN
        RETURN NULL;
      END IF;

      -- Get calendar type

      SELECT * INTO
      type
      FROM calendar.type calendar_type
      WHERE calendar_type.typeid = typeidchar;

      gregorianj := to_char(gregorian, 'J');

      IF type.typegroup IS NULL THEN

        RAISE EXCEPTION 'Calendar not supported';

      ELSIF type.typegroup = 'default' THEN

        RETURN ROW(
          date_part('year', gregorian)::integer,
          date_part('month', gregorian)::integer,
          date_part('day', gregorian)::integer
        );

      ELSIF type.typegroup = 'hebrew' THEN

        /* 15.11.4 Algorithm 6 */

        -- See errata p. 7

        m := floor(0.03386318 * (gregorianj - 347996))::integer + 1;
        yearmonthday.year := 19 * (m/235) + (19 * mod(m, 235) - 2)/235 + 1;
        k1 := calendar.hebrewfirst(yearmonthday.year);

        IF k1 > gregorianj THEN
          yearmonthday.year := yearmonthday.year - 1;
        END IF;

        /* 15.11.4 Algorithm 7 */

        a := calendar.hebrewfirst(yearmonthday.year);
        b := calendar.hebrewfirst(yearmonthday.year + 1);
        k1 := b - a - 352 - 27 * (mod(7 * yearmonthday.year + 13, 19)/12);
        c := gregorianj - a + 1;

        SELECT *
        INTO hebrewmonth
        FROM calendar.hebrewmonth
        WHERE hebrewmonth.k = k1
        AND hebrewmonth.a < c
        ORDER BY hebrewmonth.m DESC;

        yearmonthday.month := hebrewmonth.m;
        yearmonthday.day := c - hebrewmonth.a;

        /* Not part of algorithm */

        -- Determine if leap year

        SELECT * INTO
        hebrewmonth
        FROM calendar.hebrewmonth
        WHERE hebrewmonth.k = k1
        AND hebrewmonth.m = 13;

        -- If not leap year, shift months up to leave placeholder leap month

        IF hebrewmonth.a IS NULL AND yearmonthday.month > 5 THEN
          yearmonthday.month := yearmonthday.month + 1;
        END IF;

      ELSE

        /* 15.11.3 Algorithm 4 */

        f := gregorianj + type.j;

        IF type.typegroup IN ('gregorian', 'saka') THEN
          f := f + (((4 * gregorianj + type.b)/146097) * 3)/4 + type.c;
        END IF;

        e := type.r * f + type.v;
        g := mod(e, type.p)/type.r;

        IF type.typegroup = 'saka' THEN
          x := g/365;
          z := g/185 - x;
          type.s := 31 - z;
          type.w = -5 * z;
          h := type.u * g + type.w;
          yearmonthday.day := (6 * x + mod(h, type.s))/type.u + 1;
        ELSE
          h := type.u * g + type.w;
          yearmonthday.day := (mod(h, type.s))/type.u + 1;
        END IF;

        yearmonthday.month := mod(h/type.s + type.m, type.n) + 1;
        yearmonthday.year := e/type.p - type.y + (type.n + type.m - yearmonthday.month)/type.n;

      END IF;

      RETURN yearmonthday;

    END;

$$;

CREATE FUNCTION calendar.yearmonthday(historicdate calendar.historicdate) RETURNS calendar.yearmonthday
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function converts the historicdate type to the calendar.yearmonthday type.
    */

    DECLARE

        -- Intermediate values

        part calendar.part;

        -- Final values

        yearmonthday calendar.yearmonthday;

    BEGIN

        -- Skip processing for empty values

        IF historicdate IS NULL THEN
          RETURN NULL;
        END IF;

        -- Get calendar type

        SELECT *
        INTO part
        FROM calendar.part
        WHERE partid = historicdate.calendar;

        IF part.type IS NULL THEN
          RAISE EXCEPTION 'Calendar not supported';
        END IF;

        -- Convert from date

        yearmonthday := calendar.yearmonthday(historicdate.gregorian, part.type);
        IF calendar.date(yearmonthday, part.type) <> historicdate.gregorian THEN
          RAISE EXCEPTION 'Calendar conversion check failed';
        END IF;

        -- Adjust from logical year for month-day combinations on or after year start in calendar

        IF part.yearafter <> 0 AND ((yearmonthday.month > part.monthstart) OR (yearmonthday.month = part.monthstart AND yearmonthday.day >= part.daystart)) THEN
          yearmonthday.year := yearmonthday.year - part.yearafter;
        END IF;

        -- Adjust from logical year for month-day combinations before year start in calendar

        IF part.yearbefore <> 0 AND ((yearmonthday.month < part.monthstart) OR (yearmonthday.month = part.monthstart AND yearmonthday.day < part.daystart)) THEN
          yearmonthday.year := yearmonthday.year - part.yearbefore;
        END IF;

        -- Adjust to logical month

        IF part.monthdifference <> 0 THEN
          yearmonthday.month := yearmonthday.month - part.monthdifference;
          IF yearmonthday.month > part.monthmax THEN
            yearmonthday.month := yearmonthday.month - part.monthmax;
          ELSIF yearmonthday.month < 1 THEN
            yearmonthday.month := yearmonthday.month + part.monthmax;
          END IF;
        END IF;
		
		RETURN yearmonthday;

    END;

$$;

CREATE FUNCTION calendar.date(yearmonthday calendar.yearmonthday, typeidchar "char") RETURNS date
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        Derived from: Richards, E. G. "Calendars." In Explanatory Supplement to the Astronomical Almanac,
          3rd ed., edited by Sean E. Urban and P. Kenneth Seidelmann, 585-624. Mill Valley, Calif.:
          University Science Books, 2012. https://aa.usno.navy.mil/downloads/c15_usb_online.pdf
        Note: Mathematical principles, formulas, algorithms, or equations are not copyrightable. See
          U.S. COPYRIGHT OFFICE, COMPENDIUM OF U.S. COPYRIGHT OFFICE PRACTICES § 313.3(A) (3d ed. 2021).
    */

    DECLARE

      hebrewmonth calendar.hebrewmonth;
      type calendar.type;

      a integer;
      b integer;
      e bigint;
      f bigint;
      g bigint;
      h bigint;
      j bigint;
      k1 bigint;
      z bigint;

    BEGIN

      -- Skip processing for empty values

      IF yearmonthday IS NULL THEN
        RETURN NULL;
      END IF;

      -- Get calendar type

      SELECT * INTO
      type
      FROM calendar.type calendar_type
      WHERE calendar_type.typeid = typeidchar;

      IF type.typegroup IS NULL THEN

        RAISE EXCEPTION 'Calendar not supported';

      ELSIF type.typegroup = 'default' THEN

        RETURN make_date(yearmonthday.year, yearmonthday.month, yearmonthday.day);

      ELSIF type.typegroup = 'hebrew' THEN

        /* 15.11.4 Algorithm 8 */

        a := calendar.hebrewfirst(yearmonthday.year);
        b := calendar.hebrewfirst(yearmonthday.year + 1);
        k1 := b - a - 352 - 27 * (mod(7 * yearmonthday.year + 13, 19)/12);

        /* Begin addition */

        -- Determine if leap year

        SELECT * INTO
        hebrewmonth
        FROM calendar.hebrewmonth
        WHERE hebrewmonth.k = k1
        AND hebrewmonth.m = 13;

        -- If not leap year, shift months down to fill placeholder leap month

        IF hebrewmonth.a IS NULL AND yearmonthday.month > 5 THEN
          yearmonthday.month := yearmonthday.month - 1;
        END IF;

        /* End addition */

        SELECT * INTO
        hebrewmonth
        FROM calendar.hebrewmonth
        WHERE hebrewmonth.k = k1
        -- The algorithm calls for M - 1, but M appears to be correct
        AND hebrewmonth.m = yearmonthday.month;

        j := a + hebrewmonth.a + yearmonthday.day - 1;

      ELSE

        /* 15.11.3 Algorithm 3 */

        h := yearmonthday.month - type.m;
        g := yearmonthday.year + type.y - (type.n - h)/type.n;
        f := mod(h - 1 + type.n, type.n);
        e := (type.p * g + type.q)/type.r + yearmonthday.day - 1 - type.j;

        IF type.typegroup = 'saka' THEN
          z := f/6;
          j := e + ((31 - z) * f + 5 * z)/type.u;
        ELSE
          j := e + (type.s * f + type.t)/type.u;
        END IF;

        IF type.typegroup IN ('gregorian', 'saka') THEN
          j := j - (3 * ((g + type.a)/100))/4 - type.c;
        END IF;

      END IF;

      RETURN ('J' || j)::date;

    END;

$$;

CREATE FUNCTION calendar.historicdatetext(historicdate calendar.historicdate) RETURNS text
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function converts the historicdate type to the serialized calendar.historicdatetext type.
    */

    DECLARE

        -- Intermediate values

        part calendar.part;
        yearmonthday calendar.yearmonthday;
        monthtext text;
        qualifier text;

        -- Final values

        datetext text := '';

    BEGIN

        -- Skip processing for empty values

        IF historicdate IS NULL THEN
          RETURN '';
        END IF;

        -- Convert from date

        yearmonthday := calendar.yearmonthday(historicdate);

        -- Get calendar type

        SELECT *
        INTO part
        FROM calendar.part
        WHERE partid = historicdate.calendar;

        /* YEAR */

        -- Convert qualifier

        SELECT qualifier.qualifierabbreviation
        INTO qualifier
        FROM calendar.qualifier
        WHERE qualifier.qualifierid = historicdate.qualifier;

        IF qualifier IS NULL THEN
          RAISE EXCEPTION 'Invalid qualifier type';
        END IF;

        datetext := datetext || qualifier;

        -- Convert year double (before);
        --   adjust to logical year for double year (before)

        IF historicdate.yeardouble = 'before_implied' THEN
          datetext := datetext || '[';
          yearmonthday.year := yearmonthday.year + 1;
        END IF;

        -- Convert negative years

        IF yearmonthday.year < 0 THEN
          datetext := datetext || 'm';
          yearmonthday.year := abs(yearmonthday.year);
        END IF;

        -- Add year integer

        datetext := datetext || yearmonthday.year::text;

        -- Convert year double (after)

        IF historicdate.yeardouble = 'after' THEN
          datetext := datetext || '*';
        ELSIF historicdate.yeardouble = 'after_implied' THEN
          datetext := datetext || '[';
        END IF;

        /* MONTH */

        datetext := datetext || '-';

        -- Convert precision

        IF historicdate.precision IN ('year') THEN
          datetext := datetext || '~';
        END IF;

        -- Add month name

        SELECT monthshort
        INTO monthtext
        FROM calendar.monthshort
        WHERE monthshort.type = part.monthshorttype
        AND monthshort.monthinteger = yearmonthday.month;

        IF monthtext IS NULL THEN
          RAISE EXCEPTION 'Month does not exist.';
        END IF;

        datetext := datetext || monthtext;

        /* DAY */

        datetext := datetext || '-';

        -- Convert precision (before)

        IF historicdate.precision IN ('month', 'year') THEN
          datetext := datetext || '~';
        END IF;

        -- Add day integer

        datetext := datetext || lpad(yearmonthday.day::text, 2, '0');

        -- Convert precision (after)

        IF historicdate.precision = 'none' THEN
          datetext := datetext || '~';
        END IF;

        -- Add calendar

        datetext := datetext || part.partid;

        /* RETURN */

        RETURN datetext;

    END;

$$;

--
-- CREATE FUNCTIONS
--

CREATE FUNCTION calendar.daterange(historicdaterange calendar.historicdaterange) RETURNS daterange
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    BEGIN

        RETURN daterange((lower(historicdaterange)).gregorian, (upper(historicdaterange)).gregorian);

    END;

$$;

CREATE FUNCTION calendar.historicdate(datetext text) RETURNS calendar.historicdate
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function converts the serialized historicdatetext type to the historicdate type.
    */

    DECLARE

        -- Intermediate values

        part calendar.part;

        allimprecise boolean := false;
        dayimprecise boolean := false;
        daytext text := '';
        imprecision integer := 0;
        monthdaycheck boolean;
        monthimprecise boolean := false;
        monthtext text := '';
        yearmonthday calendar.yearmonthday;
        yearmultiplier integer := 1;
        yeartext text := '';

        -- Final values

        gregorian date := NULL;
        precision text := 'none';
        qualifier text := '';
        yeardouble text := 'none';
        calendar text := '';

    BEGIN

        -- Skip processing for empty values;
        --   Verify that exactly 3 date segments are present if not empty;
        --   Verify not of historicdaterangetext type

        IF datetext = '' THEN
          RETURN NULL;
        ELSIF length(datetext) - length(replace(datetext, '-', '')) <> 2 THEN
          RAISE EXCEPTION 'Missing date segment';
        ELSIF datetext ~ '[/]' THEN
          RAISE EXCEPTION 'Not historicdatetext type';
        END IF;

        -- Break up date into segments

        yeartext := split_part(datetext, '-', 1);
        monthtext := split_part(datetext, '-', 2);
        daytext := split_part(datetext, '-', 3);

        /* YEAR */

        -- Verify no extra whitespace

        IF yeartext <> trim(yeartext) THEN
          RAISE EXCEPTION 'Extra whitespace in year segment';
        END IF;

        -- Retrieve date qualifier and normalize

        IF left(yeartext, 1) !~ '^(\d|[[!m])$' THEN
          SELECT qualifier.qualifierid
          INTO qualifier
          FROM calendar.qualifier
          WHERE qualifier.qualifierabbreviation = left(yeartext, 1);

          IF qualifier IS NULL THEN
            RAISE EXCEPTION 'Invalid qualifier type';
          END IF;

          yeartext := substring(yeartext FROM 2);
        END IF;

        -- Determine if should be double year (before)

        IF left(yeartext, 1) ~ '^[[]$' THEN
          IF left(yeartext, 1) = '[' THEN
            yeardouble := 'before_implied';
          END IF;
          yeartext := substring(yeartext FROM 2);
        END IF;

        -- Determine if year is negative

        IF left(yeartext, 1) = 'm' THEN
          yearmultiplier := -1;
          yeartext := substring(yeartext FROM 2);
        END IF;

        -- Determine if should be double year (after)

        IF right(yeartext, 1) ~ '^[*[!]$' THEN
          IF yeardouble <> 'none' THEN
            RAISE EXCEPTION 'Year may only have one double character';
          ELSIF right(yeartext, 1) = '*' THEN
            yeardouble := 'after';
          ELSIF right(yeartext, 1) = '[' THEN
            yeardouble := 'after_implied';
          END IF;
          yeartext := substring(yeartext FOR length(yeartext) - 1);
        END IF;

        -- Convert year to integer

        IF yeartext !~ '^[1-9]\d*$' THEN
          RAISE EXCEPTION 'Incorrect year segment format';
        ELSE
          yearmonthday.year := yeartext::integer * yearmultiplier;
        END IF;

        -- Adjust from logical year for double year (before)

        IF yeardouble = 'before_implied' THEN
          yearmonthday.year := yearmonthday.year - 1;
        END IF;

        /* MONTH */

        -- Verify no extra whitespace

        IF monthtext <> trim(monthtext) THEN
          RAISE EXCEPTION 'Extra whitespace in month segment';
        END IF;

        -- Determine if precision month

        IF left(monthtext, 1) = '~' THEN
          monthimprecise := true;
          imprecision := imprecision + 1;
          monthtext := substring(monthtext FROM 2);
        END IF;

        -- Wait to convert month to integer until after calendar conversion

        /* DAY */

        -- Determine if precision day

        IF left(daytext, 1) = '~' THEN
          dayimprecise := true;
          imprecision := imprecision + 1;
          daytext := substring(daytext FROM 2);
        END IF;

        -- Convert day to integer

        IF daytext !~ '^\d{2}' THEN
          RAISE EXCEPTION 'Incorrect numeric day segment format';
        ELSE
          yearmonthday.day := left(daytext, 2)::integer;
          daytext := substring(daytext FROM 3);
        END IF;

        -- Determine if all precision

        IF left(daytext, 1) = '~' THEN
          allimprecise := true;
          imprecision := imprecision + 1;
          daytext := substring(daytext FROM 2);
        END IF;

        -- Change remainder to calendar

        calendar := daytext;

        /* PRECISION */

        -- Assign precision type

        IF imprecision >= 3 OR imprecision < 0 OR (imprecision = 1 AND monthimprecise) OR (imprecision = 2 AND allimprecise) THEN
          RAISE EXCEPTION 'Incorrect approximation format';
        ELSIF imprecision = 0 THEN
          precision := 'day';
        ELSIF imprecision = 2 THEN
          precision := 'year';
        ELSIF dayimprecise THEN
          precision := 'month';
        ELSIF allimprecise THEN
          precision := 'none';
        ELSE
          RAISE EXCEPTION 'Incorrect approximation format';
        END IF;

        /* CALENDAR CONVERSION */

        -- Get calendar type

        SELECT *
        INTO part
        FROM calendar.part
        WHERE partid = calendar;

        IF part.type IS NULL THEN
          RAISE EXCEPTION 'Calendar not supported';
        END IF;

        -- Convert month to integer

        SELECT monthinteger
        INTO yearmonthday.month
        FROM calendar.monthshort
        WHERE monthshort.type = part.monthshorttype
        AND monthshort.monthshort = monthtext;

        IF yearmonthday.month IS NULL THEN
          RAISE EXCEPTION 'Month does not exist.';
        END IF;

        -- Verify if date possible (no leap year check)

        SELECT COALESCE(yearmonthday.day <= monthday.monthday, FALSE)
        INTO monthdaycheck
        FROM calendar.monthday
        WHERE monthday.type = part.monthdaytype
        AND monthday.monthinteger = yearmonthday.month;

        IF NOT monthdaycheck THEN
          RAISE EXCEPTION 'Month-day combination not possible';
        END IF;

        -- Adjust to logical month

        IF part.monthdifference <> 0 THEN
          yearmonthday.month := yearmonthday.month + part.monthdifference;
          IF yearmonthday.month > part.monthmax THEN
            yearmonthday.month := yearmonthday.month - part.monthmax;
          ELSIF yearmonthday.month < 1 THEN
            yearmonthday.month := yearmonthday.month + part.monthmax;
          END IF;
        END IF;

        -- Adjust to logical year for month-day combinations before year start in calendar

        IF part.yearbefore <> 0 AND ((yearmonthday.month < part.monthstart) OR (yearmonthday.month = part.monthstart AND yearmonthday.day < part.daystart)) THEN
          yearmonthday.year := yearmonthday.year + part.yearbefore;
        END IF;

        -- Adjust to logical year for month-day combinations on or after year start in calendar

        IF part.yearafter <> 0 AND ((yearmonthday.month > part.monthstart) OR (yearmonthday.month = part.monthstart AND yearmonthday.day >= part.daystart)) THEN
          yearmonthday.year := yearmonthday.year + part.yearafter;
        END IF;

        -- Convert to date

        gregorian := calendar.date(yearmonthday, part.type);
        IF calendar.yearmonthday(gregorian, part.type) <> yearmonthday THEN
          RAISE EXCEPTION 'Calendar conversion check failed';
        END IF;

        /* RETURN */

        RETURN ROW(
          gregorian,
          precision,
          qualifier,
          yeardouble,
          calendar
        );

    END;

$$;

CREATE FUNCTION calendar.historicdate(monarchname text, regnalyearnumbervalue integer, month integer, day integer, partidvalue text) RETURNS calendar.historicdate
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function converts a regnal date to the historicdate type.
    */

    DECLARE

        -- Intermediate values

        partvalue calendar.part;
        regnalyearvalue calendar.regnalyear;
        regnalyeardaterange daterange;
        minyear integer;
        maxyear integer;
        comparedate calendar.historicdate;

        -- Final values

        finaldate calendar.historicdate;

    BEGIN

        -- Get regnal year

        SELECT *
        INTO regnalyearvalue
        FROM calendar.regnalyear
        WHERE regnalyear.monarch = monarchname
        AND regnalyear.regnalyearnumber = regnalyearnumbervalue;

        IF regnalyearvalue.regnalyeardaterangetext IS NULL THEN
          RAISE EXCEPTION 'No matching regnal year';
        END IF;

        regnalyeardaterange = regnalyearvalue.regnalyeardaterangetext::calendar.historicdaterange::daterange;

        -- Get calendar type

        SELECT *
        INTO partvalue
        FROM calendar.part
        WHERE part.partid = partidvalue;

        IF partvalue.type IS NULL THEN
          RAISE EXCEPTION 'Calendar not supported';
        END IF;

        -- Get plausible logical or historical years

        minyear := (date_part('year', lower(regnalyeardaterange)))::integer - 2;
        maxyear := (date_part('year', upper(regnalyeardaterange)))::integer + 2;

        -- Loop through possible years to see if any have matching dates

        WHILE minyear <= maxyear LOOP
          comparedate = (minyear || '-' || lpad(month::text, 2, '0') || '-' || lpad(day::text, 2, '0') || partvalue.partid)::calendar.historicdate;
          IF regnalyeardaterange @> comparedate::date THEN
            IF finaldate IS NOT NULL THEN
              RAISE EXCEPTION 'Multiple matching regnal dates';
            ELSE
              finaldate := comparedate;
            END IF;
          END IF;
          minyear := minyear + 1;
        END LOOP;

        -- Return matching date

        IF finaldate IS NULL THEN
          RAISE EXCEPTION 'No matching regnal date';
        ELSE
          RETURN finaldate;
        END IF;

    END;

$$;

CREATE FUNCTION calendar.historicdatetextformat(historicdate calendar.historicdate, formattype text, localecode text) RETURNS text
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function formats the historicdate type as a full-text date with multilingual support.
        NOTE: Qualifiers only support English.
    */

    DECLARE

        -- Intermediate values

        locale calendar.locale;
        part calendar.part;
        yearmonthday calendar.yearmonthday;
        daytext text := '';
		monthtext text;
        qualifier text;

        -- Final values

        datetext text := '';

    BEGIN

        -- Skip processing for empty values

        IF historicdate IS NULL THEN
          RETURN '';
        END IF;

        -- Convert from date

        yearmonthday := calendar.yearmonthday(historicdate);

        -- Get calendar type

        SELECT *
        INTO part
        FROM calendar.part
        WHERE partid = historicdate.calendar;

        -- Check formattype

        IF formattype NOT IN ('long', 'short') THEN
          RAISE EXCEPTION 'Format not supported';
        END IF;
        
        -- Get locale type

        SELECT *
        INTO locale
        FROM calendar.locale
        WHERE localeid = localecode;

        IF locale.localeid IS NULL THEN
          RAISE EXCEPTION 'Locale not supported';
        END IF;       

        /* MONTH */

        -- Convert precision

        IF historicdate.precision NOT IN ('year') THEN

          -- Add month name

          SELECT CASE
              WHEN formattype = 'short' THEN monthabbreviation
              ELSE monthlong
          END
          INTO monthtext
          FROM calendar.monthlong
          WHERE monthlong.type = part.monthlongtype
          AND monthlong.monthinteger = yearmonthday.month
          AND monthlong.locale = locale.localeid;

          IF monthtext IS NULL THEN
            RAISE EXCEPTION 'Month does not exist.';
          END IF;

          monthtext := monthtext || locale.monthdelimiter || ' ';

        END IF;      

        /* DAY */

        -- Convert precision

        IF historicdate.precision NOT IN ('month', 'year') THEN

          daytext := yearmonthday.day || locale.daysuffix || CASE
              WHEN yearmonthday.day = 1 THEN locale.dayonesuffix
              ELSE ''
          END || locale.daydelimiter || ' ';

          IF locale.dayfirst THEN
            datetext := daytext || monthtext;
          ELSE
            datetext := monthtext || daytext;
          END IF;

        ELSIF monthtext IS NOT NULL THEN

          datetext := monthtext;

        END IF;

        /* YEAR */

        -- Convert qualifier

        SELECT CASE
            WHEN formattype = 'short' AND qualifierlocale.qualifiershort IS NOT NULL THEN qualifierlocale.qualifiershort
            WHEN formattype = 'short' THEN qualifier.qualifiershort
            WHEN formattype = 'long' AND qualifierlocale.qualifierlong IS NOT NULL THEN qualifierlocale.qualifierlong
            ELSE qualifier.qualifierid
        END
        INTO qualifier
        FROM calendar.qualifier
        LEFT JOIN calendar.qualifierlocale
          ON qualifier.qualifierid = qualifierlocale.qualifier
          AND qualifierlocale.locale = locale.localeid
        WHERE qualifier.qualifierid = historicdate.qualifier;

        IF qualifier IS NULL THEN
          RAISE EXCEPTION 'Invalid qualifier type';
        END IF;

        datetext := qualifier || ' ' || datetext;

        -- Adjust from logical year for double year (before);
        --   convert year double (before)

        IF historicdate.yeardouble = 'before_implied' THEN
          yearmonthday.year := yearmonthday.year + 1;
          datetext := datetext || '[' || (yearmonthday.year - 1)::text || '/]';
        END IF;

        -- Add year integer

        datetext := datetext || yearmonthday.year::text;

        -- Convert year double (after)

        IF historicdate.yeardouble LIKE 'after%' THEN
          IF historicdate.yeardouble = 'after_implied' THEN
            datetext := datetext || '[';
          END IF;
          datetext := datetext || '/' || (yearmonthday.year + 1)::text;
          IF historicdate.yeardouble = 'after_implied' THEN
            datetext := datetext || ']';
          END IF;
        END IF;

        -- Trim

        datetext := trim(datetext);

        -- Convert precision (after)

        IF historicdate.precision = 'none' THEN
          datetext := '~ ' || datetext;
        END IF;

        /* RETURN */

        RETURN datetext;

    END;

$$;

CREATE FUNCTION calendar.historicdaterange(daterangetext text) RETURNS calendar.historicdaterange
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function converts the serialized historicdaterangetext type to the historicdaterange type.
    */

    DECLARE

      fromhistoricdatetext text;
      fromhistoricdate calendar.historicdate;
      tohistoricdatetext text;
      tohistoricdate calendar.historicdate;
      isinstant boolean := FALSE;

    BEGIN

      -- Skip processing for empty values

      IF daterangetext = '' THEN
        RETURN NULL;
      END IF;

      -- Verify that appropriate date range segments are present

      IF length(daterangetext) - length(replace(daterangetext, '/', '')) > 1 THEN
        RAISE EXCEPTION 'Extra date range segment';
      ELSIF length(daterangetext) - length(replace(daterangetext, '/', '')) = 0 THEN
        daterangetext := daterangetext || '/' || daterangetext;
        isinstant = TRUE;
      ELSIF split_part(daterangetext, '/', 1) = split_part(daterangetext, '/', 2) THEN
        RAISE EXCEPTION 'Date range instant formatted incorrectly';
      END IF;

      -- Break up date into segments

      fromhistoricdatetext := split_part(daterangetext, '/', 1);
      tohistoricdatetext := split_part(daterangetext, '/', 2);

      -- Convert segments into historicdate type

      fromhistoricdate := calendar.historicdate(fromhistoricdatetext);
      tohistoricdate := calendar.historicdate(tohistoricdatetext);

      -- Check identical dates in ranges;
      --   add from and to qualifiers for ranges

      IF NOT isinstant AND fromhistoricdate IS NOT NULL AND tohistoricdate IS NOT NULL THEN
        IF fromhistoricdate.gregorian = tohistoricdate.gregorian THEN
          RAISE EXCEPTION 'Instant should not be expressed as a range';
        ELSIF fromhistoricdate.qualifier = '' AND tohistoricdate.qualifier = '' THEN
          fromhistoricdate.qualifier = 'from';
          tohistoricdate.qualifier = 'to';
        END IF;
      END IF;

      -- Add extra day to tohistoricdate

      IF tohistoricdate IS NOT NULL THEN
        tohistoricdate.gregorian := tohistoricdate.gregorian + 1;
      END IF;

      -- Before

      IF isinstant AND fromhistoricdate IS NOT NULL AND fromhistoricdate.qualifier = 'before' AND tohistoricdate IS NOT NULL AND tohistoricdate.qualifier = 'before' THEN
        fromhistoricdate := ROW(
	      '-infinity'::date,
	      fromhistoricdate."precision",
	      '',
	      fromhistoricdate.yeardouble,
	      fromhistoricdate.calendar
        );
      END IF;

      -- After

      IF isinstant AND fromhistoricdate IS NOT NULL AND fromhistoricdate.qualifier = 'after' AND tohistoricdate IS NOT NULL AND tohistoricdate.qualifier = 'after' THEN
        tohistoricdate := ROW(
	      'infinity'::date,
	      tohistoricdate."precision",
	      '',
	      tohistoricdate.yeardouble,
	      tohistoricdate.calendar
        );
      END IF;

      -- Check date order

      IF fromhistoricdate IS NOT NULL AND tohistoricdate IS NOT NULL AND fromhistoricdate.gregorian >= tohistoricdate.gregorian THEN
        RAISE EXCEPTION 'From date after before date';
      ELSIF NOT (
        (NOT isinstant AND fromhistoricdate.qualifier = 'between' AND tohistoricdate.qualifier = 'and') OR
        (NOT isinstant AND fromhistoricdate.qualifier = 'from' AND tohistoricdate.qualifier = 'to') OR
        (isinstant AND fromhistoricdate.qualifier = '' AND fromhistoricdate.gregorian = '-infinity' AND tohistoricdate.qualifier = 'before') OR
        (isinstant AND fromhistoricdate.qualifier = 'after' AND tohistoricdate.qualifier = '' AND tohistoricdate.gregorian = 'infinity') OR
        (isinstant AND fromhistoricdate.qualifier = '' AND tohistoricdate.qualifier = '')
      ) THEN
        RAISE EXCEPTION 'Qualifier mismatch';
      END IF;

      /* RETURN */

      RETURN calendar.historicdaterange(
        fromhistoricdate,
        tohistoricdate
      );

    END;

$$;

CREATE FUNCTION calendar.historicdaterangetext(historicdaterange calendar.historicdaterange) RETURNS text
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function converts the historicdaterange type to the serialized historicdaterangetext type.
    */

    DECLARE

      fromhistoricdate calendar.historicdate;
      tohistoricdate calendar.historicdate;
      fromtext text;
      totext text;

    BEGIN

      -- Skip processing for empty values

      IF historicdaterange IS NULL THEN
        RETURN '';
      END IF;

      -- Extract historicdate types

      fromhistoricdate = lower(historicdaterange);
      tohistoricdate = upper(historicdaterange);

      -- Remove extra day from tohistoricdate

      IF tohistoricdate.gregorian IS NOT NULL THEN
        tohistoricdate.gregorian := tohistoricdate.gregorian - 1;
      END IF;

      -- Convert parts to text;
      --   skip processing of infinity dates

      IF tohistoricdate.qualifier <> 'before' THEN
        fromtext = calendar.historicdatetext(fromhistoricdate);
      END IF;

      IF fromhistoricdate.qualifier <> 'after' THEN
        totext = calendar.historicdatetext(tohistoricdate);
      END IF;

      IF (fromhistoricdate.qualifier = '' AND tohistoricdate.qualifier = '') OR fromhistoricdate.qualifier = 'after' THEN
        RETURN fromtext;
      ELSIF tohistoricdate.qualifier = 'before' THEN
        RETURN totext;
      ELSE
        RETURN fromtext || '/' || totext;
      END IF;

    END;

$$;

CREATE FUNCTION calendar.historicdaterangetextformat(historicdaterange calendar.historicdaterange, formattype text, localecode text) RETURNS text
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function formats the historicdaterange type as a full-text date with multilingual support.
        NOTE: Qualifiers only support English.
    */

    DECLARE

      fromhistoricdate calendar.historicdate;
      tohistoricdate calendar.historicdate;
      fromtext text;
      totext text;

    BEGIN

      -- Skip processing for empty values

      IF historicdaterange IS NULL THEN
        RETURN '';
      END IF;

      -- Extract historicdate types

      fromhistoricdate = lower(historicdaterange);
      tohistoricdate = upper(historicdaterange);

      -- Remove extra day from tohistoricdate

      IF tohistoricdate.gregorian IS NOT NULL THEN
        tohistoricdate.gregorian := tohistoricdate.gregorian - 1;
      END IF;

      -- Convert parts to text;
      --   skip processing of infinity dates

      IF tohistoricdate.qualifier <> 'before' THEN
        fromtext = calendar.historicdatetextformat(fromhistoricdate, formattype, localecode);
      END IF;

      IF fromhistoricdate.qualifier <> 'after' THEN
        totext = calendar.historicdatetextformat(tohistoricdate, formattype, localecode);
      END IF;

      IF (fromhistoricdate.qualifier = '' AND tohistoricdate.qualifier = '') OR fromhistoricdate.qualifier = 'after' THEN
        RETURN fromtext;
      ELSIF tohistoricdate.qualifier = 'before' THEN
        RETURN totext;
      ELSE
        RETURN fromtext || ' ' || totext;
      END IF;

    END;

$$;

CREATE FUNCTION calendar.regnalyeartext(historicdate calendar.historicdate) RETURNS text
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

    /*
        This function converts a historicdate type to a regnal date.
    */

    DECLARE

        -- Intermediate values

        regnalyearcursor refcursor;
        regnalyearcount integer := 0;
        lastregnalyearrow calendar.regnalyear;
        regnalyearrow calendar.regnalyear;

    BEGIN

        -- Get regnal year

        OPEN regnalyearcursor FOR
        SELECT *
        FROM calendar.regnalyear
        WHERE regnalyear.regnalyeardaterangetext::calendar.historicdaterange::daterange @> historicdate::date;

        -- Determine how many regnal years returned

        LOOP
          lastregnalyearrow := regnalyearrow;

          FETCH regnalyearcursor INTO regnalyearrow;

          IF NOT FOUND THEN
            EXIT;
          END IF;

          regnalyearcount := regnalyearcount + 1;
        END LOOP;

        -- Return regnal year if only 1;
        --   otherwise, return blank

        IF regnalyearcount = 0 THEN
          -- No matching regnal year
          RETURN '';
        ELSIF regnalyearcount > 1 THEN
          -- Multiple matching regnal years
          RETURN '';
        ELSE
          RETURN lastregnalyearrow.regnalyearnumber::text || ' ' || lastregnalyearrow.monarch;
        END IF;

    END;

$$;

--
-- ADD TABLE PRIMARY KEY CONSTRAINTS
--

ALTER TABLE ONLY calendar.hebrewmonth
    ADD CONSTRAINT hebrewmonth_pk PRIMARY KEY (k, m);

ALTER TABLE ONLY calendar.locale
    ADD CONSTRAINT locale_pk PRIMARY KEY (localeid);

ALTER TABLE ONLY calendar.monthday
    ADD CONSTRAINT monthday_pk PRIMARY KEY (type, monthinteger);

ALTER TABLE ONLY calendar.monthlong
    ADD CONSTRAINT monthlong_pk PRIMARY KEY (type, locale, monthinteger);

ALTER TABLE ONLY calendar.monthshort
    ADD CONSTRAINT monthshort_pk PRIMARY KEY (type, monthinteger);

ALTER TABLE ONLY calendar.part
    ADD CONSTRAINT part_pk PRIMARY KEY (partid);

ALTER TABLE ONLY calendar.qualifier
    ADD CONSTRAINT qualifier_pk PRIMARY KEY (qualifierid);

ALTER TABLE ONLY calendar.qualifierlocale
    ADD CONSTRAINT qualifierlocale_pk PRIMARY KEY (qualifier, locale);

ALTER TABLE ONLY calendar.regnalyear
    ADD CONSTRAINT regnalyear_pk PRIMARY KEY (monarch, regnalyearnumber);

ALTER TABLE ONLY calendar.type
    ADD CONSTRAINT type_pk PRIMARY KEY (typeid);

--
-- ADD TABLE FOREIGN KEY CONSTRAINTS AND INDEXES
--

CREATE INDEX fki_monthlong_locale_fk ON calendar.monthlong USING btree (locale);

ALTER TABLE ONLY calendar.monthlong
    ADD CONSTRAINT monthlong_locale_fk FOREIGN KEY (locale) REFERENCES calendar.locale(localeid) NOT VALID;

CREATE INDEX fki_part_type_fk ON calendar.part USING btree (type);

ALTER TABLE ONLY calendar.part
    ADD CONSTRAINT part_type_fk FOREIGN KEY (type) REFERENCES calendar.type(typeid) NOT VALID;

CREATE INDEX fki_qualifierlocale_locale_fk ON calendar.qualifierlocale USING btree (locale);

ALTER TABLE ONLY calendar.qualifierlocale
    ADD CONSTRAINT qualifierlocale_locale_fk FOREIGN KEY (locale) REFERENCES calendar.locale(localeid) NOT VALID;

CREATE INDEX fki_qualifierlocale_qualifier_fk ON calendar.qualifierlocale USING btree (qualifier);

ALTER TABLE ONLY calendar.qualifierlocale
    ADD CONSTRAINT qualifierlocale_qualifier_fk FOREIGN KEY (qualifier) REFERENCES calendar.qualifier(qualifierid) NOT VALID;

--
-- CREATE CASTS
--

CREATE CAST (calendar.historicdate AS date)
    WITH FUNCTION calendar.date(historicdate calendar.historicdate)
    AS ASSIGNMENT;

CREATE CAST (calendar.historicdate AS text)
    WITH FUNCTION calendar.historicdatetext(historicdate calendar.historicdate)
    AS ASSIGNMENT;

CREATE CAST (calendar.historicdaterange AS daterange)
    WITH FUNCTION calendar.daterange(historicdaterange calendar.historicdaterange)
    AS ASSIGNMENT;

CREATE CAST (calendar.historicdaterange AS text)
    WITH FUNCTION calendar.historicdaterangetext(historicdaterange calendar.historicdaterange)
    AS ASSIGNMENT;

CREATE CAST (text AS calendar.historicdate)
    WITH FUNCTION calendar.historicdate(datetext text)
    AS ASSIGNMENT;

CREATE CAST (text AS calendar.historicdaterange)
    WITH FUNCTION calendar.historicdaterange(daterangetext text)
    AS ASSIGNMENT;

--
-- CREATE CAST CHECKS
--

CREATE FUNCTION calendar.is_historicdate(text) RETURNS boolean
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

DECLARE
  qualifierisinstant boolean;

BEGIN
  PERFORM $1::calendar.historicdate;
  SELECT qualifier.qualifierisinstant
  INTO qualifierisinstant
  FROM calendar.qualifier
  WHERE qualifier.qualifierid = ($1::calendar.historicdate).qualifier;
  RETURN qualifierisinstant;
EXCEPTION WHEN OTHERS THEN
  RETURN FALSE;
end;
$$;

CREATE FUNCTION calendar.is_historicdaterange(text) RETURNS boolean
    LANGUAGE plpgsql IMMUTABLE SECURITY DEFINER
    AS $$

BEGIN
  PERFORM $1::calendar.historicdaterange;
  RETURN TRUE;
EXCEPTION WHEN OTHERS THEN
  RETURN FALSE;
end;
$$;

--
-- CREATE DOMAINS
--

CREATE DOMAIN calendar.historicdatetext AS text
    CONSTRAINT historicdatetext_check CHECK (calendar.is_historicdate(VALUE));

CREATE DOMAIN calendar.historicdaterangetext AS text
    CONSTRAINT historicdaterangetext_check CHECK (calendar.is_historicdaterange(VALUE));
