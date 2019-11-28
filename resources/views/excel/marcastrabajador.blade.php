<table>
    <thead>
        <tr>
            <th>DNI</th>
            <th>Apellidos y Nombres</th>
            <th>Marca 1</th>            
            <th>Marca 2</th>            
            <th>Marca 3</th>            
            <th>Marca 4</th>            
            <th>Marca 5</th>            
            <th>Marca 6</th>            
            <th>TOTAL</th>            
        </tr>
    </thead>
    <tbody>
        @foreach ($operadores as $key=>$operador)
            <tr>
                <td>{{ $operador->dni }}</td>
                <td>{{ $operador->nom_operador }} {{ $operador->ape_operador }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(0+1)) ? explode("@",$operador->marcas)[0] : '-' }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(1+1)) ? explode("@",$operador->marcas)[1] : '-' }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(2+1)) ? explode("@",$operador->marcas)[2] : '-' }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(3+1)) ? explode("@",$operador->marcas)[3] : '-' }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(4+1)) ? explode("@",$operador->marcas)[4] : '-' }}</td>
                <td>{{ (count(explode("@",$operador->marcas))>=(5+1)) ? explode("@",$operador->marcas)[5] : '-' }}</td>
                <td>{{ $operador->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>