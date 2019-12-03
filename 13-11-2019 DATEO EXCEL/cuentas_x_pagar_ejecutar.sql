--exec sp_executesql N'exec TI_REPORTES_CONSOLIDADO_SALDOSXPAGAR_ORIGEN @P1 , @P2 , @P3 ',N'@P1 varchar(3),@P2 varchar(3),@P3 varchar(8)','001','   ',''
exec TI_SALDOSXPAGAR_ORIGEN '001', '001'