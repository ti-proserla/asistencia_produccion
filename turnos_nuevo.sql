SELECT 
		dni, 
		nom_operador,
		ape_operador,
		semana,
		marcador.labor_id,
		marcador.area_id,
		marcador.proceso_id,
		CASE WHEN marcador.dia=1 THEN acumulado ELSE 0 END lunes,
		CASE WHEN marcador.dia=2 THEN acumulado ELSE 0 END martes,
		CASE WHEN marcador.dia=3 THEN acumulado ELSE 0 END miercoles,
		CASE WHEN marcador.dia=4 THEN acumulado ELSE 0 END jueves,
		CASE WHEN marcador.dia=5 THEN acumulado ELSE 0 END viernes,
		CASE WHEN marcador.dia=6 THEN acumulado ELSE 0 END sabado,
		CASE WHEN marcador.dia=7 THEN acum.ulado ELSE 0 END domingo
FROM operador
INNER JOIN
(
	SELECT 
		marcador.codigo_operador,
		DATE_FORMAT(marcador.ingreso, '%x%m-%v') semana,
		IF(DAYOFWEEK(ingreso)=1,7,DAYOFWEEK(ingreso)-1) dia,	
		SUM( ROUND(TIMESTAMPDIFF(MINUTE,ingreso,IF(salida is null,ingreso,salida))/60,2) ) acumulado,
		tareo.labor_id,
		tareo.area_id,
		tareo.proceso_id,
		tareo.nom_labor
	FROM marcador
		LEFT JOIN (SELECT 
														DATE( created_at ) fecha_tareo,
														tareo.*,
														labor.nom_labor
										FROM tareo
										INNER JOIN labor
										ON labor.id=tareo.labor_id
										GROUP BY codigo_operador,fecha_tareo
				) tareo 
				ON 	tareo.codigo_operador=marcador.codigo_operador 
						AND tareo.fecha_tareo=DATE(ingreso) 
	WHERE 
				DATE_FORMAT(marcador.ingreso, '%x-%v')="2020-01" 
	GROUP BY marcador.codigo_operador,dia
) marcador 
ON marcador.codigo_operador=operador.dni
ORDER BY marcador.codigo_operador,marcador.dia ASC