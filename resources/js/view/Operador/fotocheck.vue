<template>
    <div>
        <div class="fotocheck text-center">
            <button class="oculto-impresion btn btn-warning" @click="imprimir()">Imprimir</button>
        </div>
        <div class="fotocheck text-center">
            <img :src="url(operador.foto)" alt="">
            <p><b>{{ operador.nom_operador.split(' ')[0] }} {{ operador.ape_operador.split(' ')[0] }}</b></p>
            <hr>
            <h6>Jayanca Fruits</h6>
            <barcode :value="operador.dni" height="30" width="2" fontSize="12"></barcode>
        </div>
    </div>
</template>
<style>
    @media print{
        .oculto-impresion, .oculto-impresion *{
            display: none !important;
        }
    }
    .fotocheck{
        width: 200px;
        padding: 5px;
    }
    .fotocheck p{
        margin-bottom: 0;
    }
    .fotocheck img{
        height: 100px;
        width: auto;
        border: 1px solid #000;
    }
</style>
<script>
import VueBarcode from 'vue-barcode';
export default {
    components:{
        'barcode': VueBarcode 
    },
    data() {
        return {
            operador:null,
            id: this.$route.params.id
        }
    },
    mounted() {
        axios.get(url_base+'/operador/'+this.id)
        .then(response => {
            this.operador = response.data;
        })
    },
    methods: {
        url(foto){
            return url_base+'/../storage/operador/'+foto;
        },
        imprimir() {
            window.print();
        },
    },
}
</script>