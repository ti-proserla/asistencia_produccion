<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte de Pendientes</h4>
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
                            <th>Ingreso</th>
                            <th>Salida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in reporte">
                            <td>{{ item.dni }}</td>
                            <td>{{ item.nom_operador }} {{ item.ape_operador }}</td>
                            <td>{{ item.turno_id }}</td>
                            <td>{{ item.ingreso }}</td>
                            <td>{{ item.salida }}</td>
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
        this.listar();
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
            axios.get(url_base+'/reporte/pendientes-regularizar')
            .then(response => {
                this.reporte = response.data;
            })
        }
    },
}
</script>