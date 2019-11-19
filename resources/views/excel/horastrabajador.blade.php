<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Cod.Trabajador</th>
            <th>Apellidos y Nombres</th>
            <th>Periodo</th>
            <th>Cod.Actividad</th>
            <th>Cod.Labor</th>
            <th>Cod.Consumidor</th>
            <th>Cod. Orden Produccion</th>
            <th>Tipo Asistencia</th>
            <th>Dia 01</th>
            <th>Dia 02</th>
            <th>Dia 03</th>
            <th>Dia 04</th>
            <th>Dia 05</th>
            <th>Dia 06</th>
            <th>Dia 07</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($operadores as $key=>$operador)
            <tr>
                <td>{{ str_pad($key+1, 4, "0", STR_PAD_LEFT) }}</td>
                <td>{{ $operador->codigo."" }}</td>
                <td>{{ $operador->NombreApellido }}</td>
                <td>{{ $operador->periodo }}</td>
                <td>{{ $operador->codActividad }}</td>
                <td>{{ $operador->codLabor }}</td>
                <td>{{ $operador->codProceso }}</td>
                <td></td>
                <td>N</td>
                <td>{{ $operador->Lunes }}</td>
                <td>{{ $operador->Martes }}</td>
                <td>{{ $operador->Miercoles }}</td>
                <td>{{ $operador->Jueves }}</td>
                <td>{{ $operador->Viernes }}</td>
                <td>{{ $operador->Sabado }}</td>
                <td>{{ $operador->Domingo }}</td>
            </tr>
        @endforeach
    </tbody>
</table>