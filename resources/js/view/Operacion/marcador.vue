<template>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div v-if="operador!=null" class="fotocheck text-center">
                        <img src="https://cdn1.iconfinder.com/data/icons/user-avatars-2/300/10-512.png" alt="">
                        <p><b>{{ operador.nom_operador.split(' ')[0] }} {{ operador.ape_operador.split(' ')[0] }}</b></p>
                        <hr>
                        <h6>Jayanca Fruits</h6>
                        <barcode :value="id" height="30" width="2" fontSize="12"></barcode>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            operador: null
        }
    },
    methods: {
        
    },
}
</script>