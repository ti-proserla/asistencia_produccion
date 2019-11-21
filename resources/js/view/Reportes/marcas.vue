<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte de Marcas</h4>
            </div>
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-sm-3">
                        <select v-model="turno_id" class="form-control">
                            <option v-for="turno in turnos" :value="turno.id">{{ turno.descripcion }}</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button @click="listar()" class="btn btn-info">
                            Buscar
                        </button>
                    </div>
                </div>
                 <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nombre y Apellidos</th>
                            <th>Marca 1</th>
                            <th>Marca 2</th>
                            <th>Marca 3</th>
                            <th>Marca 4</th>
                            <th>Marca 5</th>
                            <th>Marca 6</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in reporte">
                            <td>{{ item.dni }}</td>
                            <td>{{ item.nom_operador }} {{ item.ape_operador }}</td>
                            <td>{{ item.marcas.split('@')[0]}}</td>
                            <td>{{ item.marcas.split('@')[1]}}</td>
                            <td>{{ item.marcas.split('@')[2]}}</td>
                            <td>{{ item.marcas.split('@')[3]}}</td>
                            <td>{{ item.marcas.split('@')[4]}}</td>
                            <td>{{ item.marcas.split('@')[5]}}</td>
                            <td>{{ item.total}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            reporte:[],
            turno_id: 0,
            turnos:[]
        }
    },
    mounted() {
        this.listarTurnos();
    },
    computed: {
        url(){
            return url_base+'/'+this.consulta.year+'/'+this.consulta.week;
        }
    },
    methods: {
        listarTurnos(){
            axios.get(url_base+'/turno?all=true')
            .then(response => {
                this.turnos = response.data;
                if (this.turnos.length>0) {
                    this.turno_id=this.turnos[0].id;
                }
            });
        },
        listar(){
            axios.get(url_base+'/reporte-marcas?turno_id='+this.turno_id)
            .then(response => {
                this.reporte = response.data;
            })
        }
    },
}
</script>