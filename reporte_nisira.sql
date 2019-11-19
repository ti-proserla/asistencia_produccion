SELECT 
	operador.dni As codigo,
	CONCAT(operador.nom_operador,' ',operador.ape_operador) As NombreApellido,
	CONCAT(YEAR(ingreso),MONTH(ingreso),'-',WEEK(ingreso)) AS periodo,
	area.codigo As codActividad,
	labor.codigo AS codLabor,
	proceso.codigo As codProceso,
	labor.nom_labor,
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Lunes, 
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Martes, 
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Miercoles, 
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Jueves, 
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Viernes, 
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Sabado,
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Domingo
FROM operador inner join marcador on operador.id=marcador.operador_id
LEFT JOIN (SELECT * FROM tareo WHERE WEEK(tareo.created_at)=45) AS T on T.operador_id=operador.id
left JOIN labor on labor.id=T.labor_id
LEFT JOIN area on area.id=labor.area_id
LEFT JOIN proceso on proceso.id=T.proceso_id
WHERE WEEK(ingreso)=45
GROUP BY operador.dni
