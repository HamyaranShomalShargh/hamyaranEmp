<template>
    <div>
        <input type="file" hidden class="form-control iranyekan text-center" v-on:change="SelectFiles" multiple :id="id" :name="name" :accept="file_extensions">
        <input type="text" class="form-control iranyekan text-center file_selector_box" v-on:click="PopUpFileBrowser" :id="filename_id" readonly :value="filename">
        <small class="iransans green-color d-block mt-2">{{information}}</small>
    </div>
</template>

<script>
export default {
    name: "MultipleFileBrowser",
    mounted() {
        if (this.$props.already)
            this.filename = "فایل(ها) بارگذاری شده است"
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
            return this.$props.file_box_name ? this.$props.file_box_name : "upload_files[]";
        },
        id: function (){
            return this.$props.file_box_id ? this.$props.file_box_id : "upload_files";
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
        SelectFiles(e){
            let valid_ext = this.$props.accept;
            let error_ext = [];
            let error_size = [];
            let file_names = [];
            let ext_str = '';
            let size_str = '';
            for (let i = 0; i < e.target.files.length; i++) {
                let file_ext = e.target.files[i].name.split('.').pop();
                let file_size = parseInt(e.target.files[i].size);
                if (valid_ext.indexOf(file_ext.toLowerCase()) === -1)
                    error_ext.push(e.target.files[i].name)
                if (file_size > this.$props.size)
                    error_size.push(`${e.target.files[i].name}(${Math.ceil((file_size / 1000)).toString()} KB)`);
                file_names.push(e.target.files[i].name);
            }
            if (error_ext.length > 0)
                ext_str = "<h6 style='color: red'>فرمت فایل(های) ذیل مورد قبول نمی باشد:</h6>" + error_ext.toString();
            if (error_size.length > 0)
                size_str = "<h6 style='color: red'>حجم فایل(های) ذیل مورد قبول نمی باشد:</h6>" + error_size.toString();
            if (error_size.length > 0 || error_ext.length > 0) {
                bootbox.alert({
                    "message": ext_str + size_str,
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    },
                });
                this.filename = 'فایلی انتخاب نشده است';
            } else
                this.filename = file_names.toString();
        }
    }
}
</script>

<style scoped>
.file_selector_box{
    cursor: pointer;
}
</style>
