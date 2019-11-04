<template>
    <div class="form-group">
        <div class="form-group-dragon" :class="{'focus': focus, 'active' : withContent }">
            <div class="form-group-content">
                <label for="">{{ title }}</label>
                <input @focus="OpenFocus" @blur="exitFocus" :type="type" ref="text" :value="value" @input="updateFormControl">
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "dg-input",
    props:['type','title','pName','pId','value'],
    data() {
        return {
            focus: false,
        }
    },
    computed: {
        withContent(){
            if (this.type=="date"){
                return true
            }else{
                if (this.value==null) {
                    return false;
                }
                if (this.value.length==0) {
                    return false;
                }else{
                    return true;
                }
            }
        }
    },
    methods: {
        OpenFocus(){
            this.focus=true;
        },
        exitFocus(){
            this.focus=false;
        },
        updateFormControl(){
            this.$emit('input',this.$refs.text.value);
        }
    },
}
</script>