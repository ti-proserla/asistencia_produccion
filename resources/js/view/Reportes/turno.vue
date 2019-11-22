<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Reporte Horas por Semana</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-1">
                        <label for="" class="my-2"><b>Año/Semana:</b></label>
                    </div>
                    <div class="col-6 col-sm-2">
                        <input type="number" v-model="consulta.year" class="form-control">
                    </div>
                    <div class="col-6 col-sm-1">
                        <input type="number" v-model="consulta.week" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <button @click="listar()" class="btn btn-info">
                            Buscar
                        </button>
                        <a :href="url" class="btn btn-success">Excel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                    
                <table class="table">
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nombre y Apellidos</th>
                            <th>Periodo</th>
                            <th>Cod. Actividad</th>
                            <th>Cod.Labor</th>
                            <th>Cod.Proceso</th>
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
                        <tr v-for="item in reporte">
                            <td>{{ item.codigo }}</td>
                            <td>{{ item.NombreApellido }} </td>
                            <td>{{ item.periodo }} </td>
                            <td>{{ item.codActividad }} </td>
                            <td>{{ item.codLabor }} </td>
                            <td>{{ item.codProceso }} </td>
                            <td>{{ item.Lunes }}</td>
                            <td>{{ item.Martes }}</td>
                            <td>{{ item.Miercoles }}</td>
                            <td>{{ item.Jueves }}</td>
                            <td>{{ item.Viernes }}</td>
                            <td>{{ item.Sabado }}</td>
                            <td>{{ item.Domingo }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- {{reporte}} -->
    </div>
</template>
<script>
import Input from '../../dragon-desing/dg-input.vue'
export default {
    components:{
        Input
    },
    data() {
        return {
            reporte:[],
            consulta:{
                year: moment().format('YYYY'),
                week: moment().week()
            }
        }
    },
    computed: {
        url(){
            return url_base+'/horas-semana/'+this.consulta.year+'/'+this.consulta.week;
        }
    },
    methods: {
        listar(){
            axios.get(url_base+'/reporte-turno2?year='+this.consulta.year+'&week='+this.consulta.week)
            .then(response => {
                this.reporte = response.data;
            })
        }
    },
}
</script>