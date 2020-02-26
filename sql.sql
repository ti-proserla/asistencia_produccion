SELECT 	codigo_operador,
				fecha_ref,
				DATE_FORMAT(MIN(ingreso),'%H:%i') i,
				-- IF(ingreso<CURDATE(),'SI','NO'),
				DATE_FORMAT(MAX(salida),'%H:%i') s,
				
				IF(
					DATE_FORMAT(MIN(ingreso),'%H:%i') < DATE_FORMAT('22:00') AND 
					'22:00' < DATE_FORMAT(MAX(salida),'%H:%i'
					
				),'YES','NO'),
				TIMESTAMPDIFF(MINUTE,MIN(ingreso),MAX(salida))/60,
			-- 	+ 24 * IF(DATE_FORMAT(MAX(salida),'%H:%i') > DATE_FORMAT(MIN(ingreso),'%H:%i'),0,1), 
				DATE_FORMAT(MAX(salida),'%H:%i') - DATE_FORMAT(MIN(ingreso),'%H:%i') + 24 * IF(DATE_FORMAT(MAX(salida),'%H:%i') > DATE_FORMAT(MIN(ingreso),'%H:%i'),0,1)
FROM marcador 
-- WHERE codigo_operador= '46121150' AND fecha_ref ="2020-02-17" 
GROUP BY codigo_operador,fecha_ref

LIMIT 20