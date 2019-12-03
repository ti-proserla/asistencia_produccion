USE PROSERLA
GO
SET ANSI_NULLS, QUOTED_IDENTIFIER ON
GO
-- exec TI_SALDOSXPAGAR_ORIGEN_SIN_GPF '001', ''
-- drop procedure TI_SALDOSXPAGAR_ORIGEN;

CREATE PROCEDURE TI_SALDOSXPAGAR_ORIGEN_SIN_GPF
    @IDEMPRESA CHAR(3),-- CODIGO DE EMPRESA
    @AMBITO CHAR(3) -- AMBITO DE DOCUMENTOS POR COBRAR
AS
BEGIN
	DECLARE 
		@FECHAFIN CHAR(8) -- HASTA (YYYYMMDD)

	SELECT 
		@FECHAFIN = FORMAT(GETDATE(),'yyyyMMdd')

    SELECT  *,
            ROW_NUMBER() OVER (PARTITION BY R.IDEMPRESA, R.IDCOBRARPAGARDOC ORDER BY R.IDEMPRESA, R.IDCOBRARPAGARDOC) AS NRO
    INTO    #TMP_DCOBRARPAGARDOC
    FROM    (SELECT DISTINCT
                    C.IDEMPRESA,
                    C.IDCOBRARPAGARDOC,
                    D.IDCONSUMIDOR,
                    CO.DESCRIPCION AS CONSUMIDOR
                FROM   COBRARPAGARDOC C
                    INNER JOIN DCOBRARPAGARDOC D ON D.IDEMPRESA = C.IDEMPRESA AND D.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
                    LEFT JOIN CONSUMIDOR CO ON CO.IDEMPRESA = D.IDEMPRESA AND CO.IDCONSUMIDOR = D.IDCONSUMIDOR
                WHERE  CONVERT(VARCHAR(8), C.FECHAREGISTRO, 112) <= @FECHAFIN
                    AND C.ORIGEN = 'P'
                    AND C.IDESTADO <> 'AN'
                    AND C.IDEMPRESA = @IDEMPRESA
                    AND ((C.IDDOCUMENTO IN (SELECT  IDDOCUMENTO
                                            FROM    DAMBITOXPAGAR WITH (NOLOCK)
                                            WHERE   IDAMBITO = @AMBITO AND IDEMPRESA = @IDEMPRESA)
                            AND LEN(RTRIM(@AMBITO)) > 0)
                            OR LEN(RTRIM(@AMBITO)) = 0)
                    AND RTRIM(LTRIM(ISNULL(D.IDCONSUMIDOR, ''))) > '') AS R


    SELECT  DISTINCT
            C.IDEMPRESA,
            C.IDCOBRARPAGARDOC,
            STUFF((SELECT DISTINCT
                            ', ' + RTRIM(LTRIM(ISNULL(T.IDCONSUMIDOR, '')))
                    FROM     #TMP_DCOBRARPAGARDOC T
                    WHERE    T.IDEMPRESA = C.IDEMPRESA AND T.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
            FOR   XML PATH(''), TYPE).value('.', 'VARCHAR(MAX)'), 1, 2, '') AS IDCONSUMIDOR,
            STUFF((SELECT DISTINCT
                            ', ' + RTRIM(LTRIM(ISNULL(T.CONSUMIDOR, '')))
                    FROM     #TMP_DCOBRARPAGARDOC T
                    WHERE    T.IDEMPRESA = C.IDEMPRESA AND T.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
            FOR   XML PATH(''), TYPE).value('.', 'VARCHAR(MAX)'), 1, 2, '') AS CONSUMIDOR
    INTO    #TMP_CONSUMIDORES
    FROM    COBRARPAGARDOC C
            INNER JOIN DCOBRARPAGARDOC D ON D.IDEMPRESA = C.IDEMPRESA AND D.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
    WHERE   CONVERT(VARCHAR(8), C.FECHAREGISTRO, 112) <= @FECHAFIN
            AND C.ORIGEN = 'P'
            AND C.IDESTADO <> 'AN'
            AND C.IDEMPRESA = @IDEMPRESA
            AND ((C.IDDOCUMENTO IN (SELECT  IDDOCUMENTO
                                    FROM    DAMBITOXPAGAR WITH (NOLOCK)
                                    WHERE   IDAMBITO = @AMBITO AND IDEMPRESA = @IDEMPRESA)
                    AND LEN(RTRIM(@AMBITO)) > 0)
                    OR LEN(RTRIM(@AMBITO)) = 0)
            AND RTRIM(LTRIM(ISNULL(D.IDCONSUMIDOR, ''))) > ''

    SELECT  *
    INTO    #TMP
    FROM    (SELECT C.IDCLIEPROV,
                    CL.RAZON_SOCIAL,
                    C.IDDOCUMENTO + ' ' +ltrim(rtrim( C.SERIE)) + '-' + ltrim(rtrim(C.NUMERO)) AS DOCUMENTO,
                    C.FECHA,
                    C.VENCIMIENTO,
                    CASE WHEN DATEDIFF(DAY, C.VENCIMIENTO, CONVERT(DATETIME, @FECHAFIN, 112)) < 0 THEN NULL
                            ELSE DATEDIFF(DAY, C.VENCIMIENTO, CONVERT(DATETIME, @FECHAFIN, 112))
                    END AS VENCIDO,
                    MO.NOMBRE_CORTO AS MONEDA,
                    (C.IMPORTE * D.FACTOR) AS IMPORTEMOF,
                    CAST(0.00 AS NUMERIC(17, 2)) AS PAGOSMOF,
                    SUM(M.IMPORTEMOF * M.FACTOR) AS SALDOMOF,
                    0.00 AS IMPORTEMEX,
                    0.00 AS PAGOSMEX,
                    0.00 AS SALDOMEX,
                    C.IDCOBRARPAGARDOC,
                    C.VENTANA,
                    C.GLOSA,
                    CAST(NULL AS CHAR(20)) AS NRO_DOCUMENTO,
                    CAST(NULL AS VARCHAR(4000)) AS CCOSTO,
                    CAST(NULL AS NUMERIC(17, 2)) AS RETENCIONMOF,
                    CAST(NULL AS NUMERIC(17, 2)) AS DETRACCIONMOF,
                    CAST(NULL AS NUMERIC(17, 2)) AS ANTICIPOMOF,
                    CAST(NULL AS NUMERIC(17, 2)) AS RETENCIONMEX,
                    CAST(NULL AS NUMERIC(17, 2)) AS DETRACCIONMEX,
                    CAST(NULL AS NUMERIC(17, 2)) AS ANTICIPOMEX,
                    C.ES_DETRACCION,
                    CL.CON_RETENCION,
                    T.IDCONSUMIDOR,
                    T.CONSUMIDOR,
					C.IDRUBROINV,
					RI.DESCRIPCION AS RUBROINVERSION
                FROM   MOVCTACTE M WITH (NOLOCK)
                    INNER JOIN COBRARPAGARDOC C WITH (NOLOCK) ON C.IDCOBRARPAGARDOC = M.IDREFERENCIA AND C.IDEMPRESA = M.IDEMPRESA
                    INNER JOIN CLIEPROV CL WITH (NOLOCK) ON C.IDCLIEPROV = CL.IDCLIEPROV AND C.IDEMPRESA = CL.IDEMPRESA
                    INNER JOIN DOCUMENTOS D WITH (NOLOCK) ON D.IDDOCUMENTO = C.IDDOCUMENTO AND D.IDEMPRESA = C.IDEMPRESA
                    INNER JOIN MONEDAS MO WITH (NOLOCK) ON MO.IDMONEDA = C.IDMONEDA AND MO.TIPO_MONEDA = 'N'
                    LEFT JOIN #TMP_CONSUMIDORES T ON T.IDEMPRESA = C.IDEMPRESA AND T.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
					LEFT JOIN RUBRO_INVERSION RI WITH (NOLOCK) ON C.IDEMPRESA=RI.IDEMPRESA AND C.IDRUBROINV=RI.IDRUBROINV 
                WHERE  (CONVERT(VARCHAR(8), M.FECHAREGISTRO, 112) <= @FECHAFIN)
                    AND C.ORIGEN = 'P'
                    AND C.IDESTADO <> 'AN'
                    AND C.IDEMPRESA = @IDEMPRESA
                    AND ((C.IDDOCUMENTO IN (SELECT  IDDOCUMENTO
                                            FROM    DAMBITOXPAGAR WITH (NOLOCK)
                                            WHERE   IDAMBITO = @AMBITO AND IDEMPRESA = @IDEMPRESA)
                            AND LEN(RTRIM(@AMBITO)) > 0)
                            OR LEN(RTRIM(@AMBITO)) = 0)
                GROUP BY C.IDCLIEPROV, CL.RAZON_SOCIAL, C.IDDOCUMENTO, C.SERIE, C.NUMERO, C.FECHA, C.VENCIMIENTO,
                    C.IMPORTE, MO.NOMBRE_CORTO, MO.TIPO_MONEDA, D.FACTOR, C.IDCOBRARPAGARDOC, C.VENTANA, C.GLOSA,
                    C.ES_DETRACCION, CL.CON_RETENCION, T.IDCONSUMIDOR, T.CONSUMIDOR, C.IDRUBROINV, RI.DESCRIPCION
                HAVING SUM(M.IMPORTEMOF * M.FACTOR) <> 0

                UNION ALL
                SELECT C.IDCLIEPROV,
                    CL.RAZON_SOCIAL,
                    C.IDDOCUMENTO + ' ' + ltrim(rtrim(C.SERIE)) + '-' + ltrim(rtrim(C.NUMERO)) AS DOCUMENTO,
                    C.FECHA,
                    C.VENCIMIENTO,
                    CASE WHEN DATEDIFF(DAY, C.VENCIMIENTO, CONVERT(DATETIME, @FECHAFIN, 112)) < 0 THEN NULL
                            ELSE DATEDIFF(DAY, C.VENCIMIENTO, CONVERT(DATETIME, @FECHAFIN, 112))
                    END AS VENCIDO,
                    MO.NOMBRE_CORTO AS MONEDA,
                    0.00 AS IMPORTEMOF,
                    0.00 AS PAGOSMOF,
                    0.00 AS SALDOMOF,
                    (
						CASE 
						WHEN MO.TIPO_MONEDA = 'E' THEN C.IMPORTE 
						ELSE CASE 
							WHEN ISNULL(C.importemex,0) = 0
							THEN 
								CAST(C.importe * CASE WHEN ISNULL(C.tcmoneda,0) = 0 THEN 1 ELSE C.tcmoneda END AS NUMERIC(17,2))
							ELSE C.importemex
							END
						END 
					* D.FACTOR) AS IMPORTEMEX,
                    CAST(0.00 AS NUMERIC(17, 2)) AS PAGOSMEX,
                    SUM(M.IMPORTEMEX * M.FACTOR) AS SALDOMEX,
                    C.IDCOBRARPAGARDOC,
                    C.VENTANA,
                    C.GLOSA,
                    CAST(NULL AS CHAR(20)) AS NRO_DOCUMENTO,
                    CAST(NULL AS VARCHAR(4000)) AS CCOSTO,
                    CAST(NULL AS NUMERIC(17, 2)) AS RETENCIONMOF,
                    CAST(NULL AS NUMERIC(17, 2)) AS DETRACCIONMOF,
                    CAST(NULL AS NUMERIC(17, 2)) AS ANTICIPOMOF,
                    CAST(NULL AS NUMERIC(17, 2)) AS RETENCIONMEX,
                    CAST(NULL AS NUMERIC(17, 2)) AS DETRACCIONMEX,
                    CAST(NULL AS NUMERIC(17, 2)) AS ANTICIPOMEX,
                    C.ES_DETRACCION,
                    CL.CON_RETENCION,
                    T.IDCONSUMIDOR,
                    T.CONSUMIDOR,
					C.IDRUBROINV,
					RI.DESCRIPCION AS RUBROINVERSION
                FROM   MOVCTACTE M WITH (NOLOCK)
                    INNER JOIN COBRARPAGARDOC C WITH (NOLOCK) ON C.IDCOBRARPAGARDOC = M.IDREFERENCIA AND C.IDEMPRESA = M.IDEMPRESA
                    INNER JOIN CLIEPROV CL WITH (NOLOCK) ON C.IDCLIEPROV = CL.IDCLIEPROV AND C.IDEMPRESA = CL.IDEMPRESA
                    INNER JOIN DOCUMENTOS D WITH (NOLOCK) ON D.IDDOCUMENTO = C.IDDOCUMENTO AND D.IDEMPRESA = C.IDEMPRESA
                    INNER JOIN MONEDAS MO WITH (NOLOCK) ON MO.IDMONEDA = C.IDMONEDA AND MO.TIPO_MONEDA <> 'N'

                    LEFT JOIN #TMP_CONSUMIDORES T ON T.IDEMPRESA = C.IDEMPRESA AND T.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
					LEFT JOIN RUBRO_INVERSION RI WITH (NOLOCK) ON C.IDEMPRESA=RI.IDEMPRESA AND C.IDRUBROINV=RI.IDRUBROINV 
                WHERE  (CONVERT(VARCHAR(8), M.FECHAREGISTRO, 112) <= @FECHAFIN)
                    AND C.ORIGEN = 'P'
                    AND C.IDESTADO <> 'AN'
                    AND C.IDEMPRESA = @IDEMPRESA
                    AND ((C.IDDOCUMENTO IN (SELECT  IDDOCUMENTO
                                            FROM    DAMBITOXPAGAR WITH (NOLOCK)
                                            WHERE   IDAMBITO = @AMBITO AND IDEMPRESA = @IDEMPRESA)
                            AND LEN(RTRIM(@AMBITO)) > 0)
                            OR LEN(RTRIM(@AMBITO)) = 0)
                GROUP BY C.IDCLIEPROV, CL.RAZON_SOCIAL, C.IDDOCUMENTO, C.SERIE, C.NUMERO, C.FECHA, C.VENCIMIENTO,
                    C.IMPORTE, MO.NOMBRE_CORTO, MO.TIPO_MONEDA, D.FACTOR, C.IDCOBRARPAGARDOC, C.VENTANA, C.GLOSA,
                    C.ES_DETRACCION, CL.CON_RETENCION, T.IDCONSUMIDOR, T.CONSUMIDOR, C.IDRUBROINV, RI.DESCRIPCION, C.tcmoneda,
					C.importemex
                HAVING SUM(M.IMPORTEMEX * M.FACTOR) <> 0
	) R
		
    
	UPDATE  T
    SET     T.NRO_DOCUMENTO = (CASE WHEN LEN(RTRIM(ISNULL(L.RUC, ''))) = 0 THEN L.DNI ELSE L.RUC END),
            T.CCOSTO = STUFF((SELECT DISTINCT
                                        ' / ' + RTRIM(ISNULL(D.IDCONSUMIDOR, ''))
                                FROM      DCOBRARPAGARDOC D
                                WHERE     D.IDCOBRARPAGARDOC = T.IDCOBRARPAGARDOC
                                        AND D.IDEMPRESA = @IDEMPRESA
                                        AND LEN(RTRIM(ISNULL(D.IDCONSUMIDOR, ''))) <> 0
                                FOR XML PATH('')), 1, 2, ''),
            T.RETENCIONMOF = (SELECT    SUM(M.IMPORTEMOF * M.FACTOR)
                                FROM      MOVCTACTE M
                                        INNER JOIN COBRARPAGARDOC C ON M.IDORIGEN = C.IDCOBRARPAGARDOC AND M.IDEMPRESA = C.IDEMPRESA
                                WHERE     M.IDREFERENCIA = T.IDCOBRARPAGARDOC
                                        AND M.IDEMPRESA = @IDEMPRESA
                                        AND M.TABLA = 'COBRARPAGARDOC'
                                        AND C.VENTANA IN ('EDT_RETENCION')
                                        AND C.IDDOCUMENTO <> 'PCR'),
            T.DETRACCIONMOF = (SELECT   SUM(M.IMPORTEMOF * M.FACTOR)
                                FROM     MOVCTACTE M
                                        INNER JOIN INGRESOEGRESOCABA I ON M.IDORIGEN = I.IDINGRESOEGRESOCABA AND M.IDEMPRESA = I.IDEMPRESA
                                        INNER JOIN DINGRESOEGRESOCABA D ON I.IDINGRESOEGRESOCABA = D.IDINGRESOEGRESOCABA AND I.IDEMPRESA = D.IDEMPRESA
                                WHERE    M.IDREFERENCIA = T.IDCOBRARPAGARDOC
                                        AND M.IDEMPRESA = @IDEMPRESA
                                        AND D.IDPROVISION = T.IDCOBRARPAGARDOC
                                        AND M.TABLA = 'INGRESOEGRESOCABA'
                                        AND I.VENTANA IN ('EDT_LIQUIDACION_DETR')),
            T.ANTICIPOMOF = (SELECT SUM(M.IMPORTEMOF * M.FACTOR)
                                FROM   ACOBRARPAGARDOC A
                                    INNER JOIN MOVCTACTE M ON A.IDANTICIPO = M.IDREFERENCIA AND A.IDEMPRESA = M.IDEMPRESA
                                WHERE  A.IDCOBRARPAGARDOC = T.IDCOBRARPAGARDOC
                                    AND A.IDEMPRESA = @IDEMPRESA
                                    AND M.IDORIGEN <> T.IDCOBRARPAGARDOC
                                    AND M.TABLA = 'COBRARPAGARDOC'
                                    AND A.ES_ANTICIPO = 1),
            T.RETENCIONMEX = (SELECT    SUM(M.IMPORTEMEX * M.FACTOR)
                                FROM      MOVCTACTE M
                                        INNER JOIN COBRARPAGARDOC C ON M.IDORIGEN = C.IDCOBRARPAGARDOC AND M.IDEMPRESA = C.IDEMPRESA
                                WHERE     M.IDREFERENCIA = T.IDCOBRARPAGARDOC
                                        AND M.IDEMPRESA = @IDEMPRESA
                                        AND M.TABLA = 'COBRARPAGARDOC'
                                        AND C.VENTANA IN ('EDT_RETENCION')
                                        AND C.IDDOCUMENTO <> 'PCR'),
            T.DETRACCIONMEX = (SELECT   SUM(M.IMPORTEMEX * M.FACTOR)
                                FROM     MOVCTACTE M
                                        INNER JOIN INGRESOEGRESOCABA I ON M.IDORIGEN = I.IDINGRESOEGRESOCABA AND M.IDEMPRESA = I.IDEMPRESA
                                        INNER JOIN DINGRESOEGRESOCABA D ON I.IDINGRESOEGRESOCABA = D.IDINGRESOEGRESOCABA AND I.IDEMPRESA = D.IDEMPRESA
                                WHERE    M.IDREFERENCIA = T.IDCOBRARPAGARDOC
                                        AND M.IDEMPRESA = @IDEMPRESA
                                        AND D.IDPROVISION = T.IDCOBRARPAGARDOC
                                        AND M.TABLA = 'INGRESOEGRESOCABA'
                                        AND I.VENTANA IN ('EDT_LIQUIDACION_DETR')),
            T.ANTICIPOMEX = (SELECT SUM(M.IMPORTEMEX * M.FACTOR)
                                FROM   ACOBRARPAGARDOC A
                                    INNER JOIN MOVCTACTE M ON A.IDANTICIPO = M.IDREFERENCIA AND A.IDEMPRESA = M.IDEMPRESA
                                WHERE  A.IDCOBRARPAGARDOC = T.IDCOBRARPAGARDOC
                                    AND A.IDEMPRESA = @IDEMPRESA
                                    AND M.IDORIGEN <> T.IDCOBRARPAGARDOC
                                    AND M.TABLA = 'COBRARPAGARDOC'
                                    AND A.ES_ANTICIPO = 1)
    FROM    #TMP T INNER JOIN CLIEPROV L ON T.IDCLIEPROV = L.IDCLIEPROV AND L.IDEMPRESA = @IDEMPRESA
		
    UPDATE  #TMP
    SET     PAGOSMOF = IMPORTEMOF - SALDOMOF,
            PAGOSMEX = IMPORTEMEX - SALDOMEX

    SELECT  IDCLIEPROV,
            RAZON_SOCIAL,
			SUBSTRING(DOCUMENTO,1,3) AS TIPO,
            DOCUMENTO,
            FECHA,
            VENCIMIENTO,
			DATEPART(ISO_WEEK,vencimiento) as SEMANA,
            MONEDA,
            SALDOMOF AS SALDO_NACIONAL,
            SALDOMEX AS SALDO_EXTRANJERA
    FROM    #TMP
	WHERE	DOCUMENTO NOT LIKE '%ADR%' AND
			DOCUMENTO NOT LIKE '%ADV%' AND
			DOCUMENTO NOT LIKE '%AQR%' AND
			DOCUMENTO NOT LIKE '%BPP%' AND
			DOCUMENTO NOT LIKE '%CRT%' AND
			DOCUMENTO NOT LIKE '%DVA%' AND
			DOCUMENTO NOT LIKE '%NVC%' AND
			DOCUMENTO NOT LIKE '%REM%' AND
			DOCUMENTO NOT LIKE '%GPF%'
	ORDER BY idclieprov
END
GO
