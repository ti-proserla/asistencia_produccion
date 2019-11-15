SELECT 
	operador.dni,
	YEAR(ingreso) as year,
	WEEK(ingreso) as month, 
	SUM( CASE WHEN DAYOFWEEK(ingreso)=2 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Lunes, 
	SUM( CASE WHEN DAYOFWEEK(ingreso)=3 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Martes, 
	SUM( CASE WHEN DAYOFWEEK(ingreso)=4 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Miercoles, 
	SUM( CASE WHEN DAYOFWEEK(ingreso)=5 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Jueves, 
	SUM( CASE WHEN DAYOFWEEK(ingreso)=6 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Viernes, 
	SUM( CASE WHEN DAYOFWEEK(ingreso)=7 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Sabado,
	SUM( CASE WHEN DAYOFWEEK(ingreso)=1 THEN (TIMESTAMPDIFF(MINUTE,ingreso,salida)/60) ELSE 0 END) as Domingo
FROM operador inner join marcador on operador.id=marcador.operador_id
GROUP BY operador.dni