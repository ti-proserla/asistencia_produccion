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
                                <Input title="Codigo de Barras" :focusSelect="true" v-model="codigo_barras"></Input>
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
                <div class="card-body">
                    <div v-if="alert!=null" :class="'alert alert-'+alert.status" role="alert">
                        {{ alert.data }}
                    </div>
                    <div v-if="respuesta!=null && respuesta.status=='OK'"  class="fotocheck text-center" style="margin-right: auto;margin-left: auto">
                        <img :src="url(respuesta.data.foto)" alt="">
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
            turno_id: 0,
            alert: null
        }
    },
    mounted() {
        this.listarTurnos();
    },
    methods: {
        url(foto){
            return url_base+'/../storage/operador/'+foto;
        },
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
                    axios.post(url_base+'/marcador',{ codigo_barras: cod_barras_paso,turno_id: this.turno_id})
                    .then(response => {
                        this.respuesta=response.data;
                        var resp=response.data;
                        switch (resp.status) {
                            case "VALIDATION":
                                this.errors_editar=resp.data;
                                break;
                            case "OK":
                                this.alert={
                                    status: 'success',
                                    data: 'Marca Correcta.'
                                }
                                break;
                        }
                        this.clearAlert();
                    })
                }else{
                    this.codigo_barras=null;
                    this.alert={
                        status: 'danger',
                        data: 'Código no Valido'
                    }
                    this.clearAlert();
                }
            })
        },
        clearAlert(){
            setTimeout(() => {
                this.alert=null;
            }, 1000);
        }
    },
}
</script>