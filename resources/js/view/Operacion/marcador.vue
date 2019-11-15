<template>
    <div class="row">
        <div class="col-sm-6">
             <div class="card">
                 <div class="card-header">
                    <h4 class="card-title">Registro de Marcación</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="">Seleccionar Turno:</label>
                            <select class="form-control" v-model="turno_id">
                                <option v-for="turno in turnos" :value="turno.id">{{turno.descripcion}}</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <form v-on:submit.prevent="guardar()">
                                <Input title="Codigo de Barras" focusSelect="true" v-model="codigo_barras"></Input>
                                <button type="submit" hidden></button>
                            </form>
                        </div>
                    </div>
                </div>
             </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Respuesta</h4>
                </div>
                <div v-if="respuesta!=null" class="card-body">
                    <div v-if="respuesta.status=='OK'" class="alert alert-success" role="alert">
                        Marcado Correcto
                    </div>
                    <div v-else class="alert alert-danger" role="alert">
                        {{ respuesta.data }}
                    </div>
                    <div v-if="respuesta.status=='OK'"  class="fotocheck text-center" style="margin-right: auto;margin-left: auto">
                        <img src="https://cdn1.iconfinder.com/data/icons/user-avatars-2/300/10-512.png" alt="">
                        <p><b>{{ respuesta.data.nom_operador.split(' ')[0] }} {{ respuesta.data.ape_operador.split(' ')[0] }}</b></p>
                        <hr>
                        <h6>Jayanca Fruits</h6>
                    </div>
                </div>
            </div>
        </div>
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
            turnos:[],
            codigo_barras: null,
            respuesta: null,
            turno_id: 0
        }
    },
    mounted() {
        this.listarTurnos();
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
        guardar(){
            this.$nextTick(() =>{
                if (this.codigo_barras.length==8) {
                    var cod_barras_paso=this.codigo_barras;
                    this.codigo_barras=null;
                    axios.post(url_base+'/marcacion',{ codigo_barras: cod_barras_paso,turno_id: this.turno_id})
                    .then(response => {
                        this.respuesta=response.data;
                    })
                }else{
                    this.codigo_barras=null;
                    this.respuesta={
                        status: 'ERROR',
                        data: 'Código no Valido'
                    }
                    // console.log('codigo de barras no valido');
                    // alert('codigo de barras no valido');
                }
            })
        }
    },
}
</script>