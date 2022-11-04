<template>
    <div>
        <input type="file" hidden class="form-control iranyekan text-center" v-on:change="SelectFile" :id="id" :name="name" :accept="file_extensions">
        <input type="text" class="form-control iranyekan text-center file_selector_box" v-on:click="PopUpFileBrowser" :id="filename_id" readonly :value="filename">
        <small class="iransans green-color d-block mt-2">{{information}}</small>
    </div>
</template>

<script>
export default {
    name: "MultipleFileBrowser",
    mounted() {
        if (this.$props.already)
            this.filename = "فایل بارگذاری شده است"
    },
    data() {
        return {
            filename: "فایلی انتخاب نشده است",
            information: "* فرمت های قابل قبول " + `(${this.$props.accept.toString()})` + " / حداکثر سایز قابل قبول " + `(${numeral(this.$props.size / 1000).format('0,0')} کیلوبایت)`,
        }
    },
    computed : {
        file_extensions : function (){
            return this.$props.accept.map(extension => '.' + extension).join(',');
        },
        name: function (){
            return this.$props.file_box_name ? this.$props.file_box_name : "upload_file";
        },
        id: function (){
            return this.$props.file_box_id ? this.$props.file_box_id : "upload_file";
        },
        filename_id (){
            return this.$props.filename_box_id ? this.$props.filename_box_id : "file_browser_box";
        }
    },
    props:["accept","size","already","file_box_name","file_box_id","filename_box_id"],
    methods:{
        PopUpFileBrowser(e){
            $(e.target).closest('div').find('input[type="file"]').click();
        },
        SelectFile(e){
            let valid_ext = this.$props.accept;
            let file_ext = e.target.files[0].name.split('.').pop();
            let file_size = parseInt(e.target.files[0].size);
            if (valid_ext.indexOf(file_ext.toLowerCase()) === -1){
                bootbox.alert({
                    "message": "فرمت فایل مورد قبول نمی باشد",
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    },
                });
                this.filename = 'فایلی انتخاب نشده است';
            }
            else if (file_size > this.$props.size){
                bootbox.alert({
                    "message": "حجم فایل انتخاب شده بیشتر از 300 کیلوبایت می باشد",
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    },
                });
                this.filename = 'فایلی انتخاب نشده است';
            }
            else
                this.filename = e.target.files[0].name;
        }
    }
}
</script>
<style>
.file_selector_box{
    cursor: pointer;
}
</style>
