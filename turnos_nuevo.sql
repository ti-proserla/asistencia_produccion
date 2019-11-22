select 
	operador.id,
	operador.dni As codigo, 
	CONCAT(operador.nom_operador,' ',operador.ape_operador) As NombreApellido, 
	CONCAT(YEAR(ingreso),MONTH(ingreso),'-',WEEK(ingreso,3)) AS periodo, 
	area.codigo As codActividad, 
	labor.codigo AS codLabor, proceso.codigo As codProceso,
	labor.nom_labor,
	ingreso,
	salida,
	-- ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Lunes, 
	-- ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Martes, 
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Miercoles,
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Jueves, 
	ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Viernes
	-- ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Sabado,
	-- ROUND(SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END),2) as Domingo 
from `operador` 
	inner join `marcador` on `operador`.`id` = `marcador`.`operador_id` 
	left join tareo as T
		on (`T`.`operador_id` = marcador.`operador_id`) AND ( DATE(T.created_at) = DATE(marcador.ingreso) ) 
	left join `labor` on `labor`.`id` = `T`.`labor_id`
	left join `area` on `area`.`id` = `labor`.`area_id` 
	left join `proceso` on `proceso`.`id` = `T`.`proceso_id` 
	where WEEK(ingreso,3) = 47 and YEAR(ingreso) = 2019 
	group by `codigo`, DATE(ingreso)