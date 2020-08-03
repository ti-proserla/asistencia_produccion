DELETE FROM tareo
where id IN(
    SELECT * 
    FROM( 
        SELECT min(id) as id
        FROM tareo 
        Group by codigo_operador,fecha,labor_id
        HAVING COUNT(id)>1
    ) AS TAR
)