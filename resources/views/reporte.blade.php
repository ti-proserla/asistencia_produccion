<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes de conteo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        .navbar-brand{
            margin-left: auto;
            margin-right: auto
        }
        .card-header{
            background-color: #eee;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-light bg-light text-center">
            <a class="navbar-brand" href="#"><img src="http://www.providperu.org/website/Asociados/Proserla.png" alt=""></a>
        </nav>
        <div class="container">
            <h1 class="text-center">Reportes de Conteo</h1>
            <div class="row">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 form-group">
                                    <label for="">Fecha Inicio</label>
                                    <input type="datetime-local" v-model="fecha_inicio" class="form-control">
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="">Fecha Fin</label>
                                    <input type="datetime-local" v-model="fecha_fin" class="form-control">
                                </div>
                                <div class="col-lg-2 form-group">
                                    <br>
                                    <button @click="listar" class="btn btn-info">
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="card">
                        <div class="card-header">
                            Conteo
                        </div>
                        <div class="card-body">
                            <h4 v-if="conteos!=null">@{{ conteos.length }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Fecha y Hora</th>
                        <th>COD PRODUCTO</th>
                        <th>COD LINEA</th>
                        <th>COD BARRAS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(conteo,index) in conteos">
                        <td>@{{ index+1 }}</td>
                        <td>@{{ conteo.created_at }}</td>
                        <td>@{{ conteo.cod_producto }}</td>
                        <td>@{{ conteo.cod_linea }}</td>
                        <td>@{{ conteo.cod_barras }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/moment.js')}}"></script>
    <script>
        var app=new Vue({
            el: "#app",
            data() {
                return {
                    conteos:null,
                    fecha_inicio: moment().format('YYYY-MM-DDT00:00'),
                    fecha_fin: moment().format('YYYY-MM-DDTHH:mm'),
                    // hora_inicio: moment().format('HH:mm')
                }
            },
            mounted() {
                this.listar();
            },
            methods: {
                listar(){
                    axios
                    .get('https://192.168.1.242/api/conteo?fi='+this.fecha_inicio+'&ff='+this.fecha_fin)
                    .then(response => {
                        app.conteos = response.data;
                    })
                }
            },

        })
    </script>

</body>
</html>