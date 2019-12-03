USE PROSERLA_301019
GO
SET ANSI_NULLS, QUOTED_IDENTIFIER ON
GO
-- exec TI_SALDOSXCOBRAR_ORIGEN '001','','<?xml version = "1.0" encoding="Windows-1252" standalone="yes"?><VFPData><tvendedor><idvendedor/><descripcion>SIN VENDEDOR</descripcion></tvendedor></VFPData>'
-- exec TI_SALDOSXCOBRAR_ORIGEN '001', ''
-- drop procedure TI_SALDOSXCOBRAR_ORIGEN;

CREATE PROCEDURE TI_SALDOSXCOBRAR_ORIGEN
    @IDEMPRESA CHAR(3),-- CODIGO DE EMPRESA
    @AMBITO CHAR(3),-- AMBITO DE DOCUMENTOS POR COBRAR
    @XMLVENDEDOR TEXT
AS
    BEGIN
		DECLARE
			@IDCAMPANA CHAR(4) = '',
			@IDPROVEEDOR CHAR(11)='',
			@FECHAFIN CHAR(8) -- HASTA (YYYYMMDD)
		SELECT 
			@FECHAFIN = FORMAT(GETDATE(),'yyyyMMdd')

		IF @IDCAMPANA is null
		BEGIN
			SET @IDCAMPANA = ''
		END

        DECLARE @HDOC INT, @FILTRARXCAMPANA VARCHAR(20), @IMP_FISE CHAR(3)


		SELECT @FILTRARXCAMPANA  = ISNULL(VALOR,'NO')
		FROM PEMPRESA WHERE IDEMPRESA = @IDEMPRESA AND RTRIM(LTRIM(PARAMETRO)) = 'HABCAMPANA_CAB'

		IF @FILTRARXCAMPANA IS NULL
		BEGIN
			SET @FILTRARXCAMPANA = 'NO'
		END

		--VENDEDOR
        EXEC Sp_xml_preparedocument @hdoc OUTPUT, @XMLVENDEDOR

        SELECT  idvendedor = mixml.idvendedor,
                descripcion = mixml.descripcion
        INTO    #tvendedor
        FROM    OPENXML (@hdoc, 'VFPData/tvendedor', 2)
                WITH ( idvendedor CHAR(3),
						descripcion VARCHAR(50)) AS mixml

        EXEC Sp_xml_removedocument @hdoc


		SELECT @IMP_FISE = RTRIM(VALOR) FROM PEMPRESA WHERE PARAMETRO = 'IMP_FISE' AND IDEMPRESA = @IDEMPRESA




		-- FISE
		SELECT	I.IDCOBRARPAGARDOC, 
				SUM(I.IMPUESTO) AS IMP_FISE
		INTO	#TMP_FISE
		FROM	ICOBRARPAGARDOC_OTROS I
		WHERE	I.IDEMPRESA = @IDEMPRESA
				AND I.IDIMPUESTO = @IMP_FISE
		GROUP BY I.IDCOBRARPAGARDOC

        IF LEN(RTRIM(@AMBITO)) > 0
            BEGIN
                SELECT  *
                INTO    #TMP
                FROM    (SELECT C.IDCLIEPROV,
                                CL.RAZON_SOCIAL,
                                C.IDDOCUMENTO + ' ' + ltrim(rtrim(C.SERIE)) + '-' + ltrim(rtrim(C.NUMERO))  AS DOCUMENTO,
                                C.GLOSA,
                                T.DESCRIPCION AS VENDEDOR,
                                C.FECHA,
                                C.VENCIMIENTO,
                                CAST(CONVERT(DATETIME, @FECHAFIN, 112) - ISNULL(C.VENCIMIENTO,C.FECHA) AS NUMERIC(5, 0)) AS VENCIDO,
                                MO.NOMBRE_CORTO AS MONEDA,
                                ((C.IMPORTE + ISNULL(TF.IMP_FISE, 0)) * D.FACTOR) AS IMPORTEMOF,
                                cast(0.00 as numeric(17,2)) AS PAGOSMOF,
                                SUM(M.IMPORTEMOF * M.FACTOR) AS SALDOMOF,
                                cast(0.00 as numeric(17,2)) AS IMPORTEMEX,
                                cast(0.00 as numeric(17,2)) AS PAGOSMEX,
                                cast(0.00 as numeric(17,2)) AS SALDOMEX,
                                C.IDCOBRARPAGARDOC,
                                C.VENTANA,
								C.exportacion,
								C.idtipoventa,
								tv.descripcion,
								c.idfpago,
								CASE WHEN F.DESCRIPCION IS NULL 
									THEN 
											CASE WHEN C.IDDOCUMENTO='LET' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 WHEN C.IDDOCUMENTO='LEC' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 WHEN C.IDDOCUMENTO='LED' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 ELSE '' END	
									ELSE F.DESCRIPCION END  AS FORMAPAGO,
								c.tcambio
                         FROM   MOVCTACTE M
                                JOIN COBRARPAGARDOC C WITH (NOLOCK) ON C.IDCOBRARPAGARDOC = M.IDREFERENCIA AND C.IDEMPRESA = M.IDEMPRESA
                                JOIN CLIEPROV CL WITH (NOLOCK) ON C.IDCLIEPROV = CL.IDCLIEPROV AND C.IDEMPRESA = CL.IDEMPRESA
                                JOIN DOCUMENTOS D WITH (NOLOCK) ON D.IDDOCUMENTO = C.IDDOCUMENTO AND D.IDEMPRESA = C.IDEMPRESA
								LEFT JOIN TIPOVENTA tv WITH (NOLOCK) ON tv.idtipoventa = C.idtipoventa AND tv.idempresa=C.idEmpresa
								LEFT JOIN FORMA_PAGO F WITH (NOLOCK) ON F.IDFPAGO = C.IDFPAGO AND F.idempresa=C.idEmpresa
                                JOIN MONEDAS MO WITH (NOLOCK) ON MO.IDMONEDA = C.IDMONEDA AND MO.TIPO_MONEDA = 'N'
                                inner JOIN #tvendedor T ON T.IDVENDEDOR = ISNULL(C.IDVENDEDOR, '')
								LEFT JOIN #TMP_FISE TF ON TF.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
                         WHERE  (CONVERT(VARCHAR(8), M.FECHAREGISTRO, 112) <= @FECHAFIN) AND
                                C.ORIGEN = 'C' AND
                                C.IDESTADO <> 'AN' AND
                                C.IDEMPRESA = @IDEMPRESA AND
                                C.IDDOCUMENTO IN (SELECT    IDDOCUMENTO
                                                  FROM      DAMBITOXCOBRAR WITH (NOLOCK)
                                                  WHERE     IDAMBITOXC = @AMBITO AND
                                                            IDEMPRESA = @IDEMPRESA)
                         GROUP BY C.IDCLIEPROV,
                                CL.RAZON_SOCIAL,
                                C.IDDOCUMENTO,
                                C.SERIE,
                                C.NUMERO,
                                C.GLOSA,
                                T.DESCRIPCION,
                                C.FECHA,
                                C.VENCIMIENTO,
                                C.IMPORTE,
								ISNULL(TF.IMP_FISE, 0),
                                MO.NOMBRE_CORTO,
                                MO.TIPO_MONEDA,
                                D.FACTOR,
                                C.IDCOBRARPAGARDOC,
                                C.VENTANA,
								C.exportacion,
								C.idtipoventa,
								tv.descripcion,
								c.idfpago,
								F.DESCRIPCION,
								C.DIAS,
								c.tcambio
                         HAVING SUM(M.IMPORTEMOF * M.FACTOR) <> 0
                         UNION ALL
                         SELECT C.IDCLIEPROV,
                                CL.RAZON_SOCIAL,
                                C.IDDOCUMENTO + ' ' + ltrim(rtrim(C.SERIE)) + '-' + ltrim(rtrim(C.NUMERO))  AS DOCUMENTO,
                                C.GLOSA,
                                T.DESCRIPCION AS VENDEDOR,
                                C.FECHA,
                                C.VENCIMIENTO,
                                CAST(CONVERT(DATETIME, @FECHAFIN, 112) - ISNULL(C.VENCIMIENTO,C.FECHA) AS INT) AS VENCIDO,
                                MO.NOMBRE_CORTO AS MONEDA,
                                cast(0.00 as numeric(17,2)) AS IMPORTEMOF,
                                cast(0.00 as numeric(17,2)) AS PAGOSMOF,
                                cast(0.00 as numeric(17,2)) AS SALDOMOF,
                                ((C.IMPORTE + ISNULL(TF.IMP_FISE, 0)) * D.FACTOR) AS IMPORTEMEX,
                                cast(0.00 as numeric(17,2)) AS PAGOSMEX,
                                SUM(M.IMPORTEMEX * M.FACTOR) AS SALDOMEX,
                                C.IDCOBRARPAGARDOC,
                                C.VENTANA,
								C.exportacion,
								C.idtipoventa,
								tv.descripcion,
								c.idfpago,
								CASE WHEN F.DESCRIPCION IS NULL 
									THEN 
											CASE WHEN C.IDDOCUMENTO='LET' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 WHEN C.IDDOCUMENTO='LEC' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 WHEN C.IDDOCUMENTO='LED' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 ELSE '' END	
									ELSE F.DESCRIPCION END  AS FORMAPAGO,
								c.tcambio
                         FROM   MOVCTACTE M
                                JOIN COBRARPAGARDOC C WITH (NOLOCK) ON C.IDCOBRARPAGARDOC = M.IDREFERENCIA AND C.IDEMPRESA = M.IDEMPRESA
                                JOIN CLIEPROV CL WITH (NOLOCK) ON C.IDCLIEPROV = CL.IDCLIEPROV AND C.IDEMPRESA = CL.IDEMPRESA
                                JOIN DOCUMENTOS D WITH (NOLOCK) ON D.IDDOCUMENTO = C.IDDOCUMENTO AND D.IDEMPRESA = C.IDEMPRESA
                                JOIN MONEDAS MO WITH (NOLOCK) ON MO.IDMONEDA = C.IDMONEDA AND MO.TIPO_MONEDA <> 'N'
								LEFT JOIN TIPOVENTA tv WITH (NOLOCK) ON tv.idtipoventa = C.idtipoventa AND tv.idempresa=C.idEmpresa
								LEFT JOIN FORMA_PAGO F WITH (NOLOCK) ON F.IDFPAGO = C.IDFPAGO AND F.idempresa=C.idEmpresa
                                inner JOIN #tvendedor T ON T.IDVENDEDOR = ISNULL(C.IDVENDEDOR, '')
								LEFT JOIN #TMP_FISE TF ON TF.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
                         WHERE  (CONVERT(VARCHAR(8), M.FECHAREGISTRO, 112) <= @FECHAFIN) AND
                                C.ORIGEN = 'C' AND
                                C.IDESTADO <> 'AN' AND
                                C.IDEMPRESA = @IDEMPRESA AND
                                C.IDDOCUMENTO IN (SELECT    IDDOCUMENTO
                                                  FROM      DAMBITOXCOBRAR WITH (NOLOCK)
                                                  WHERE     IDAMBITOXC = @AMBITO AND
                                                            IDEMPRESA = @IDEMPRESA)
                         GROUP BY C.IDCLIEPROV,
                                CL.RAZON_SOCIAL,
                                C.IDDOCUMENTO,
                                C.SERIE,
                                C.NUMERO,
                                C.GLOSA,
                                T.DESCRIPCION,
                                C.FECHA,
                                C.VENCIMIENTO,
                                C.IMPORTE,
								ISNULL(TF.IMP_FISE, 0),
                                MO.NOMBRE_CORTO,
                                MO.TIPO_MONEDA,
                                D.FACTOR,
                                C.IDCOBRARPAGARDOC,
                                C.VENTANA,
								C.exportacion,
								C.idtipoventa,
								tv.descripcion,
								c.idfpago,
								F.DESCRIPCION,
								C.DIAS,
								c.tcambio

                         HAVING SUM(M.IMPORTEMEX * M.FACTOR) <> 0) R

                UPDATE  #TMP
                SET     PAGOSMOF = IMPORTEMOF - SALDOMOF,
                        PAGOSMEX = IMPORTEMEX - SALDOMEX,
                        VENCIDO = CASE WHEN VENCIDO < 0 THEN 0
                                       ELSE VENCIDO
                                  END
                
				IF RTRIM(LTRIM(ISNULL(@FILTRARXCAMPANA,'NO'))) = 'SI'
				BEGIN
					

					SELECT  T.*,
							C.idcampana AS IDCAMPANA,
							FC.DESCRIPCION AS DESCCAMPANA,
							ISNULL(C.NRO_CONTENEDOR,'') AS NROCONTENEDOR
					FROM    #TMP T
					LEFT JOIN COBRARPAGARDOC C ON C.IDEMPRESA = @IDEMPRESA AND C.idcobrarpagardoc = T.idcobrarpagardoc
					LEFT JOIN FECHA_CAMPANA_ANUAL FC (NOLOCK) ON FC.IDEMPRESA = @IDEMPRESA AND C.idcampana = FC.IDFECHA
					WHERE 
						C.IDEMPRESA = @IDEMPRESA
						AND
						(
							RTRIM(LTRIM(@IDCAMPANA)) = '' OR (RTRIM(LTRIM(C.IDCAMPANA)) = RTRIM(LTRIM(@IDCAMPANA)) AND LTRIM(RTRIM(@IDCAMPANA)) <> '' )
						)
					RETURN
				END

				IF LEN(ISNULL(@IDPROVEEDOR,''))=0
					BEGIN					
						SELECT  *
						FROM    #TMP
					END
				IF LEN(ISNULL(@IDPROVEEDOR,''))<>0
					BEGIN
						SELECT  *
						FROM    #TMP
						WHERE   IDCLIEPROV=@IDPROVEEDOR
					END

            END
        ELSE
            BEGIN
				print 'aca'
                SELECT  *
                INTO    #TMP1
                FROM    (SELECT C.IDCLIEPROV,
                                CL.RAZON_SOCIAL,
                                C.IDDOCUMENTO + ' ' +ltrim(rtrim(C.SERIE)) + '-' + ltrim(rtrim(C.NUMERO)) AS DOCUMENTO,
                                C.GLOSA,
                                T.DESCRIPCION AS VENDEDOR,
                                C.FECHA,
                                C.VENCIMIENTO,
								CAST(CONVERT(DATETIME, @FECHAFIN, 112) - ISNULL(C.VENCIMIENTO,C.FECHA) AS NUMERIC(5, 0)) AS VENCIDO,
                               --datediff(DAY,CONVERT(DATETIME, @FECHAFIN, 112),ISNULL(C.VENCIMIENTO,C.fechaRegistro)) AS VENCIDO,
                                MO.NOMBRE_CORTO AS MONEDA,
                                ((C.IMPORTE + ISNULL(TF.IMP_FISE, 0)) * D.FACTOR) AS IMPORTEMOF,
                                CAST(0.00 AS NUMERIC(17, 2)) AS PAGOSMOF,
                                SUM(M.IMPORTEMOF * M.FACTOR) AS SALDOMOF,
                                cast(0.00 as numeric(17,2)) AS IMPORTEMEX,
                                cast(0.00 as numeric(17,2)) AS PAGOSMEX,
                                cast(0.00 as numeric(17,2)) AS SALDOMEX,
                                C.IDCOBRARPAGARDOC,
                                C.VENTANA,
								C.exportacion,
								C.idtipoventa,
								tv.descripcion,
								c.idfpago,
								CASE WHEN F.DESCRIPCION IS NULL 
									THEN 
											CASE WHEN C.IDDOCUMENTO='LET' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 WHEN C.IDDOCUMENTO='LEC' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 WHEN C.IDDOCUMENTO='LED' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												 ELSE '' END	
									ELSE F.DESCRIPCION END  AS FORMAPAGO,
								c.tcambio
                         FROM   MOVCTACTE M
                                JOIN COBRARPAGARDOC C WITH (NOLOCK) ON C.IDCOBRARPAGARDOC = M.IDREFERENCIA AND C.IDEMPRESA = M.IDEMPRESA
                                JOIN CLIEPROV CL WITH (NOLOCK) ON C.IDCLIEPROV = CL.IDCLIEPROV AND C.IDEMPRESA = CL.IDEMPRESA
                                JOIN DOCUMENTOS D WITH (NOLOCK) ON D.IDDOCUMENTO = C.IDDOCUMENTO AND D.IDEMPRESA = C.IDEMPRESA
								LEFT JOIN TIPOVENTA tv WITH (NOLOCK) ON tv.idtipoventa = C.idtipoventa AND tv.idempresa=C.idEmpresa
								LEFT JOIN FORMA_PAGO F WITH (NOLOCK) ON F.IDFPAGO = C.IDFPAGO AND F.idempresa=C.idEmpresa
                                JOIN MONEDAS MO WITH (NOLOCK) ON MO.IDMONEDA = C.IDMONEDA AND MO.TIPO_MONEDA = 'N'
                                inner JOIN #tvendedor T ON T.IDVENDEDOR = ISNULL(C.IDVENDEDOR, '')
								LEFT JOIN #TMP_FISE TF ON TF.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
                         WHERE  (CONVERT(VARCHAR(8), M.FECHAREGISTRO, 112) <= @FECHAFIN) AND
                                C.ORIGEN = 'C' AND
                                C.IDESTADO <> 'AN' AND
                                C.IDEMPRESA = @IDEMPRESA
                         GROUP BY C.IDCLIEPROV,
                                CL.RAZON_SOCIAL,
                                C.IDDOCUMENTO,
                                C.SERIE,
                                C.NUMERO,
                                C.GLOSA,
                                T.DESCRIPCION,
                                C.FECHA,
								C.fechaRegistro,
                                C.VENCIMIENTO,
                                C.IMPORTE,
								ISNULL(TF.IMP_FISE, 0),
                                MO.NOMBRE_CORTO,
                                MO.TIPO_MONEDA,
                                D.FACTOR,
                                C.IDCOBRARPAGARDOC,
                                C.VENTANA,
								C.exportacion,
								C.idtipoventa,
								tv.descripcion,
								c.idfpago,
								F.DESCRIPCION,
								C.DIAS,
								c.tcambio
                         HAVING SUM(M.IMPORTEMOF * M.FACTOR) <> 0
                         UNION ALL
                         SELECT C.IDCLIEPROV,
                                CL.RAZON_SOCIAL,
                                C.IDDOCUMENTO + ' ' + ltrim(rtrim(C.SERIE)) + '-' + ltrim(rtrim(C.NUMERO))  AS DOCUMENTO,
                                C.GLOSA,
                                T.DESCRIPCION AS VENDEDOR,
                                C.FECHA,
                                C.VENCIMIENTO,
								CAST(CONVERT(DATETIME, @FECHAFIN, 112) - ISNULL(C.VENCIMIENTO,C.FECHA) AS NUMERIC(5, 0)) AS VENCIDO,
                                --datediff(DAY,CONVERT(DATETIME, @FECHAFIN, 112),ISNULL(C.VENCIMIENTO,C.fechaRegistro)) AS VENCIDO,
                                MO.NOMBRE_CORTO AS MONEDA,
                                cast(0.00 as numeric(17,2)) AS IMPORTEMOF,
                                cast(0.00 as numeric(17,2)) AS PAGOSMOF,
                                cast(0.00 as numeric(17,2)) AS SALDOMOF,
                                ((C.IMPORTE + ISNULL(TF.IMP_FISE, 0)) * D.FACTOR) AS IMPORTEMEX,
                                CAST(0.00 AS NUMERIC(17, 2)) AS PAGOSMEX,
                                SUM(M.IMPORTEMEX * M.FACTOR) AS SALDOMEX,
                                C.IDCOBRARPAGARDOC,
                                C.VENTANA,
								C.exportacion,
								C.idtipoventa,
								tv.descripcion,
								c.idfpago,
								CASE WHEN F.DESCRIPCION IS NULL 
								THEN 
										CASE	WHEN C.IDDOCUMENTO='LET' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												WHEN C.IDDOCUMENTO='LEC' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												WHEN C.IDDOCUMENTO='LED' THEN 'LETRA '+ RTRIM(LTRIM(STR(C.DIAS)))+ ' DIAS'
												ELSE '' END	
								ELSE F.DESCRIPCION END  AS FORMAPAGO,
								c.tcambio
                         FROM   MOVCTACTE M
                                JOIN COBRARPAGARDOC C WITH (NOLOCK) ON C.IDCOBRARPAGARDOC = M.IDREFERENCIA AND C.IDEMPRESA = M.IDEMPRESA
                                JOIN CLIEPROV CL WITH (NOLOCK) ON C.IDCLIEPROV = CL.IDCLIEPROV AND C.IDEMPRESA = CL.IDEMPRESA
                                JOIN DOCUMENTOS D WITH (NOLOCK) ON D.IDDOCUMENTO = C.IDDOCUMENTO AND D.IDEMPRESA = C.IDEMPRESA
							    LEFT JOIN TIPOVENTA tv WITH (NOLOCK) ON tv.idtipoventa = C.idtipoventa AND tv.idempresa=C.idEmpresa
								LEFT JOIN FORMA_PAGO F WITH (NOLOCK) ON F.IDFPAGO = C.IDFPAGO AND F.idempresa=C.idEmpresa
                                JOIN MONEDAS MO WITH (NOLOCK) ON MO.IDMONEDA = C.IDMONEDA AND MO.TIPO_MONEDA <> 'N'
                                inner JOIN #tvendedor T ON T.IDVENDEDOR = ISNULL(C.IDVENDEDOR, '')
								LEFT JOIN #TMP_FISE TF ON TF.IDCOBRARPAGARDOC = C.IDCOBRARPAGARDOC
                         WHERE  (CONVERT(VARCHAR(8), M.FECHAREGISTRO, 112) <= @FECHAFIN) AND
                                C.ORIGEN = 'C' AND
                                C.IDESTADO <> 'AN' AND
                                C.IDEMPRESA = @IDEMPRESA
                         GROUP BY C.IDCLIEPROV,
                                CL.RAZON_SOCIAL,
                                C.IDDOCUMENTO,
                                C.SERIE,
                                C.NUMERO,
                                C.GLOSA,
                                T.DESCRIPCION,
                                C.FECHA,
								c.fechaRegistro,
                                C.VENCIMIENTO,
                                C.IMPORTE,
								ISNULL(TF.IMP_FISE, 0),
                                MO.NOMBRE_CORTO,
                                MO.TIPO_MONEDA,
                                D.FACTOR,
                                C.IDCOBRARPAGARDOC,
                                C.VENTANA,
								C.exportacion,
								C.idtipoventa,
								tv.descripcion,
								c.idfpago,
								F.DESCRIPCION,
								C.DIAS,
								c.tcambio
                         HAVING SUM(M.IMPORTEMEX * M.FACTOR) <> 0) R

                UPDATE  #TMP1
                SET     PAGOSMOF = IMPORTEMOF - SALDOMOF,
                        PAGOSMEX = IMPORTEMEX - SALDOMEX,
                        VENCIDO = CASE WHEN VENCIDO < 0 THEN 0
                                       ELSE VENCIDO
                                  END
                IF RTRIM(LTRIM(ISNULL(@FILTRARXCAMPANA,'NO'))) = 'SI'
				BEGIN
					SELECT  T.*,
							C.idcampana AS IDCAMPANA,
							FC.DESCRIPCION AS DESCCAMPANA,
							ISNULL(C.NRO_CONTENEDOR,'') AS NROCONTENEDOR
					FROM    #TMP1 T
					LEFT JOIN COBRARPAGARDOC C ON C.IDEMPRESA = @IDEMPRESA AND C.idcobrarpagardoc = T.idcobrarpagardoc
					LEFT JOIN FECHA_CAMPANA_ANUAL FC (NOLOCK) ON FC.IDEMPRESA = @IDEMPRESA AND C.idcampana = FC.IDFECHA
					WHERE 
						C.IDEMPRESA = @IDEMPRESA
						AND
						(
							RTRIM(LTRIM(@IDCAMPANA)) = '' OR (RTRIM(LTRIM(C.IDCAMPANA)) = RTRIM(LTRIM(@IDCAMPANA)) AND LTRIM(RTRIM(@IDCAMPANA)) <> '' )
						)

					RETURN
				END


				IF LEN(ISNULL(@IDPROVEEDOR,''))=0
					BEGIN	
						print 'aqui'
						SELECT  idclieprov,
						RAZON_SOCIAL,
						DOCUMENTO,
						fecha,
						vencimiento,
						DATEPART(ISO_WEEK,vencimiento) as SEMANA,
						MONEDA,
						SALDOMOF,
						SALDOMEX
						FROM    #TMP1
					END
				IF LEN(ISNULL(@IDPROVEEDOR,''))<>0
					BEGIN
						SELECT  *
						FROM    #TMP1
						WHERE   IDCLIEPROV=@IDPROVEEDOR
					END
            END
    END
GO