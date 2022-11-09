<template>
    <button type="button" :class="css" @click="send_data">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: "AxiosButton",
    props:["css","route","elements","record_id","action","message","required"],
    methods:{
        required_field() {
            let self = this;
            if (self.$props.required) {
                let values = [];
                for(let i = 0; i < self.$props.required.length; i++) {
                    let item = self.$props.record_id ? $(`${self.$props.required[i]}_${self.$props.record_id}`) : $(`${self.$props.required[i]}`);
                    if (item.val() === "") {
                        if ($(item).hasClass("selectpicker")) {
                            $(item).toggleClass("is-invalid");
                            $(item).closest(".bootstrap-select").find(".dropdown-toggle").toggleClass("is-invalid");
                            $('.selectpicker').selectpicker('refresh');
                        }
                        else if ($(item).is("input[type='file']"))
                            $(item).closest("div").find(".file_selector_box").toggleClass("is-invalid");
                        else
                            item.toggleClass("is-invalid");
                    }
                    else
                        values.push(item.val());
                }
                return values.length === self.$props.required.length;
            }
            else return true;
        },
        send_data(){
            let self = this;
            if(this.required_field()) {
                bootbox.confirm({
                    message: this.$props.message,
                    closeButton: false,
                    buttons: {
                        confirm: {
                            label: 'بله',
                            className: 'btn-success',
                        },
                        cancel: {
                            label: 'خیر',
                            className: 'btn-danger',
                        }
                    },
                    callback: function (result) {
                        if (result === true) {
                            bootbox.hideAll();
                            let data = new FormData();
                            if (self.$props.elements) {
                                self.$props.elements.forEach(function (item) {
                                    if (self.$props.record_id) {
                                        if ($(`${item}_${self.$props.record_id}`).is("input[type='file']"))
                                            data.append(item.replaceAll("#", '').replaceAll(".", ''), $(`${item}_${self.$props.record_id}`)[0].files[0]);
                                        else
                                            data.append(item.replaceAll("#", '').replaceAll(".", ''), $(`${item}_${self.$props.record_id}`).val());
                                    }
                                    else{
                                        if ($(item).is("input[type='file']"))
                                            data.append(item.replaceAll("#",'').replaceAll(".",''), $(item)[0].files[0]);
                                        else
                                            data.append(item.replaceAll("#",'').replaceAll(".",''), $(item).val());
                                    }
                                });
                            }
                            if (self.$props.record_id)
                                data.append('id', self.$props.record_id);
                            self.$root.$data.show_loading = true;
                            axios.post(self.$props.route, data)
                                .then(function (response) {
                                    self.$root.$data.show_loading = false;
                                    if (response.data !== null) {
                                        if (response.data.data)
                                            self.$root.$data.table_data_records = response.data.data;
                                        if (response.data.filtered_data)
                                            self.$root.$data.filtered_data = response.data.filtered_data;
                                        switch (response.data["result"]) {
                                            case "success": {
                                                alertify.notify(response.data["message"], 'success', "5");
                                                if (typeof response.data["import_errors"] !== "undefined" && response.data["import_errors"].length > 0) {
                                                    self.$root.$data.import_errors = response.data["import_errors"];
                                                    $("#import_errors").modal("show");
                                                }
                                                break;
                                            }
                                            case "fail": {
                                                alertify.notify(response.data["message"], 'warning', "20");
                                                break;
                                            }
                                        }
                                    }
                                }).catch(function (error) {
                                self.$root.$data.show_loading = false;
                                alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'warning', "20");
                            });
                        }
                    }
                })
            }
        }
    }
}
</script>
