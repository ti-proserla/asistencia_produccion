<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Consultar Marcas</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form v-on:submit.prevent="consultar()">
                                    <Input title="Codigo de Barras" type="number" :focusSelect="true" v-model="codigo_barras"></Input>
                                    <button type="submit" hidden></button>
                                </form>
                            </div>
                        </div>
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
    }    
}
</script>