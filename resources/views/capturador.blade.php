<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div id="app">

        <form v-if="iniciado==null"  v-on:submit.prevent="inicializar">
            INICIALIZAR
            <input id="txt-cod_barras" type="text" v-model="cod_barras" ref="cod_barras_ref">
            <button type="submit">Guardar</button>
        </form>
        <form v-else  v-on:submit.prevent="guardar">
            CONTEO
            <input id="txt-cod_barras" type="text" v-model="cod_barras" ref="cod_barras_ref">
            <button type="submit">Guardar</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script >
        var app=new Vue({
            el: "#app",
            data() {
                return {
                    info: null,
                    cod_barras: null,
                    iniciado: null,
                }
            },
            mounted() {
                console.log(this.$refs.cod_barras_ref.focus);
                this.seguroFocus();
            },
            methods: {
                seguroFocus(){
                    var t=this;
                    setTimeout(() => {
                        t.$refs.cod_barras_ref.focus();
                        t.seguroFocus();    
                    }, 1000);
                },
                inicializar(){
                    this.iniciado=this.cod_barras;
                    this.cod_barras=null;
                },
                guardar(){
                    var cod_barras_paso=this.cod_barras;
                    this.cod_barras=null;
                    axios
                    .post('https://192.168.1.242/api/conteo',{ cod_barras: cod_barras_paso, configuracion: this.iniciado})
                    .then(response => {
                        app.info = response.data;
                    })
                }
            },
        });
    </script>
    <!-- <php echo $lineas ?> -->
</body>
</html>