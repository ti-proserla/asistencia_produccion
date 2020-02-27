ROUND( (	
		(TIME_TO_SEC(sal) - TIME_TO_SEC( DATE_FORMAT(MIN(ingreso),'%H:%i') ))
		+ TIME_TO_SEC(IF( DATE_FORMAT(MIN(ingreso),'%H:%i')  < '22:00', IF('06:00'<  DATE_FORMAT(MIN(ingreso),'%H:%i') , DATE_FORMAT(MIN(ingreso),'%H:%i') ,'06:00'),'22:00')) 
		- TIME_TO_SEC(IF(sal < '22:00', IF('06:00'< sal,sal,'06:00'),'22:00'))
		+ IF( sal <  DATE_FORMAT(MIN(ingreso),'%H:%i')  , TIME_TO_SEC('24:00') - (TIME_TO_SEC('22:00')-TIME_TO_SEC('06:00')),0)
)/3600 , 2) h_nocturnas,