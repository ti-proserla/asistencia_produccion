<table>
    <thead>
        <tr>
            <th>DNI</th>
            <th>Apellidos y Nombres</th>
            <th>Fecha</th>
            <th>Cod.Actividad</th>
            <th>Cod.Labor</th>
            <th>Cod.Consumidor</th>
            <th>Labor</th>
            <th>Marca 1</th>            
            <th>Marca 2</th>            
            <th>Marca 3</th>            
            <th>Marca 4</th>            
            <th>H. Trabajadas</th>            
            <th>H. Nocturnas</th>            
        </tr>
    </thead>
    <tbody>
        @foreach ($operadores as $key=>$operador)
            <tr>
                <td>{{ $operador->dni }}</td>
                <td>{{ $operador->NombreApellido }}</td>
                <td>{{ $operador->fecha_ref }}</td>
                <td>{{ $operador->codActividad }}</td>
                <td>{{ $operador->codLabor }}</td>
                <td>{{ $operador->codProceso }}</td>
                <td>{{ $operador->nom_labor }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(0+1)) ? explode("@",$operador->marcas)[0] : '-' }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(1+1)) ? explode("@",$operador->marcas)[1] : '-' }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(2+1)) ? explode("@",$operador->marcas)[2] : '-' }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(3+1)) ? explode("@",$operador->marcas)[3] : '-' }}</td>
                <td>{{ $operador->h_trabajadas }}</td>
                <td>{{ $operador->h_nocturnas }}</td>
            </tr>
        @endforeach
    </tbody>
</table>