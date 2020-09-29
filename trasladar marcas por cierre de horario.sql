UPDATE 
	-- SELECT * FROM
	marcador INNER JOIN 
	(SELECT * 
		FROM marcador 
		WHERE marcador.turno=2 
		AND marcador.salida is NULL 
		AND marcador.fecha_ref='2020-09-29') HOY 
	ON HOY.codigo_operador = marcador.codigo_operador
SET marcador.salida=HOY.ingreso
WHERE marcador.fecha_ref='2020-09-28' 
	AND marcador.salida is NULL
	AND marcador.turno=2