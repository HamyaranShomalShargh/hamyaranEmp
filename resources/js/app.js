/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
window.Vue = require('vue').default;
import AutoNumeric from "autonumeric";
window.AutoNumeric = AutoNumeric;
window.numeral = require('numeral');
window.persianDate = require('persian-date');
import pDatepicker from 'm-persian-datepicker'
window.pDatepicker = pDatepicker;
window.bootbox = require('bootbox');
require('bootbox/bootbox.locales');
window.bootbox.setDefaults({
    locale: "fa",
    show: true,
    backdrop: true,
    closeButton: false,
    animate: true,
    className: "bootbox-modal"
});
import alertify from 'alertifyjs';
window.alertify = alertify;
require('bootstrap-select/dist/js/bootstrap-select');
require('bootstrap-select/js/i18n/defaults-fa_IR');
let DataTable = Vue.component('data-table', require('./components/DataTable').default);
let MultipleFileBrowser = Vue.component('m-file-browser', require('./components/MultipleFileBrowser').default);
let SingleFileBrowser = Vue.component('s-file-browser', require('./components/SingleFileBrowser').default);
let Loading = Vue.component('loading', require('./components/Loading').default);
let AxiosButton = Vue.component('axios-button', require('./components/AxiosButton').default);
let EditableDataTable = Vue.component('editable-data-table', require('./components/EditableDataTable').default);

const app = new Vue({
    el: '#app',
    data:{
        show_loading: false,
        table_data_records : typeof table_data !== "undefined" ? table_data : [],
        filtered_data : [],
        import_errors: [],
        employee_list_selection: 'contract',
        data_selectable_dropdown: '',
        roles_list: typeof flow_data !== "undefined" ? flow_data : [],
        can_edit_values: typeof edit_values_permission !== "undefined" ? edit_values_permission : false,
        new_performance_notifications: typeof new_performance_data !== "undefined" ? new_performance_data : [],
        new_invoice_notifications: typeof new_invoice_data !== "undefined" ? new_invoice_data : [],
        new_advantage_notifications: typeof new_advantage_data !== "undefined" ? new_advantage_data : [],
        attributes_list: typeof attributes_data !== "undefined" ? attributes_data : [],
        invoice_cover_list: typeof invoice_cover_data !== "undefined" ? invoice_cover_data : [],
        table_attributes_type: "performance",
        invoice_cover_data: typeof cover_data !== "undefined" ? cover_data : [],
        invoice_cover_items: typeof cover_items !== "undefined" ? cover_items : [],
        advantage_list: typeof advantage_data !== "undefined" ? advantage_data : [],
        report_json_data: typeof report_data !== "undefined" ? report_data : [],
    },
    components: {
        data_table: DataTable,
        m_file_browser: MultipleFileBrowser,
        s_file_browser: SingleFileBrowser,
        loading: Loading,
        axios_button: AxiosButton,
        editable_data_table: EditableDataTable
    },
    mounted() {
        const self = this;
        if ('serviceWorker' in navigator)
            navigator.serviceWorker.register(`/sw.js`, {scope: '/'}).then((registered) => {
                    if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                        if ('serviceWorker' in navigator && 'PushManager' in window) {
                            Notification.requestPermission().then(function (permission) {
                                navigator.serviceWorker.ready.then(registration => {
                                    const title = 'همیاران شمال شرق';
                                    const options = {
                                        body: 'سیستم پیام رسانی با موفقیت فعال شد',
                                        icon: `${window.location.protocol}//${window.location.host}/img/new_notification.png`,
                                        tag: 'renotify',
                                        renotify: true,
                                    };
                                    registration.showNotification(title, options).then(() => {
                                    });
                                });
                            });
                        } else
                            this.notification_text = "متاسفانه مرورگر دستگاه شما از سیستم پیام رسانی (Notification) پشتیبانی نمی کند";
                    }
            });
        if ($("#table_responsive").length)
            $("#table_responsive").css("max-height",`calc(100vh - 50px - ${document.getElementById("table_responsive").offsetTop}px)`);
        if (typeof window.Laravel.user !== "undefined" && window.Laravel.user) {
            Echo.private(`notifications.${window.Laravel.user}`)
                .listen('NotificationEvent', (notification) => {
                    let title = '';
                    switch (notification.type) {
                        case "performance": {
                            let duplicate = self.new_performance_notifications.find((item) => {
                                return item.action === notification.action
                            });
                            if (typeof duplicate === "undefined") {
                                let tmp = {"message": notification.message, "action": notification.action};
                                self.new_performance_notifications.push(tmp);
                            }
                            title = 'کارکرد ماهانه جدید';
                            break;
                        }
                        case "invoice": {
                            let duplicate = self.new_invoice_notifications.find((item) => {
                                return item.action === notification.action
                            });
                            if (typeof duplicate === "undefined") {
                                let tmp = {"message": notification.message, "action": notification.action};
                                self.new_invoice_notifications.push(tmp);
                            }
                            title = 'وضعیت ماهانه جدید';
                            break;
                        }
                        case "advantage": {
                            let duplicate = self.new_advantage_notifications.find((item) => {
                                return item.action === notification.action
                            });
                            if (typeof duplicate === "undefined") {
                                let tmp = {"message": notification.message, "action": notification.action};
                                self.new_advantage_notifications.push(tmp);
                            }
                            title = 'فرم تغییرات مزایای جدید';
                            break;
                        }
                    }
                    const options = {
                        body: notification.message,
                        icon: `${window.location.protocol}//${window.location.host}/images/notification.png?v=${new Date().getTime()}`,
                        data: notification,
                        tag: 'renotify',
                        renotify: true,
                        dir: 'rtl',
                    };
                    new Notification(title, options);
                    axios.post("/NewAutomationData", {type: notification.type})
                        .then(function (response) {
                            if (response.data !== null) {
                                if (response.data.data)
                                    self.table_data_records = response.data.data;
                                switch (response.data["result"]) {
                                    case "success": {
                                        alertify.notify(response.data["message"], 'success', "5");
                                        break;
                                    }
                                    case "fail": {
                                        alertify.notify(response.data["message"], 'warning', "20");
                                        break;
                                    }
                                }
                            }
                        }).catch(function (error) {
                        alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'warning', "20");
                    });
                });
        }
        // const channel = new BroadcastChannel('NewMessage');
        // channel.addEventListener('message', event => {
        //     switch (event.data.type){
        //         case "NewPerformance":{
        //             let duplicate = self.new_performance_notifications.find((item) => {
        //                 return item.action === event.data.route
        //             });
        //             if (typeof duplicate === "undefined") {
        //                 let tmp = {"message": event.data.message, "action": event.data.route};
        //                 self.new_performance_notifications.push(tmp);
        //                 axios.post("/NewPerformanceAutomationData")
        //                     .then(function (response) {
        //                         if (response.data !== null) {
        //                             if (response.data.data)
        //                                 self.table_data_records = response.data.data;
        //                             switch (response.data["result"]) {
        //                                 case "success": {
        //                                     alertify.notify(response.data["message"], 'success', "5");
        //                                     break;
        //                                 }
        //                                 case "fail": {
        //                                     alertify.notify(response.data["message"], 'warning', "20");
        //                                     break;
        //                                 }
        //                             }
        //                         }
        //                     }).catch(function (error) {
        //                     alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'warning', "20");
        //                 });
        //             }
        //             break;
        //         }
        //         case "NewAdvantage":{
        //             let duplicate = self.new_advantage_notifications.find((item) => {
        //                 return item.action === event.data.route
        //             });
        //             if (typeof duplicate === "undefined") {
        //                 let tmp = {"message": event.data.message, "action": event.data.route};
        //                 self.new_advantage_notifications.push(tmp);
        //                 axios.post("/NewAdvantageAutomationData")
        //                     .then(function (response) {
        //                         if (response.data !== null) {
        //                             if (response.data.data)
        //                                 self.table_data_records = response.data.data;
        //                             switch (response.data["result"]) {
        //                                 case "success": {
        //                                     alertify.notify(response.data["message"], 'success', "5");
        //                                     break;
        //                                 }
        //                                 case "fail": {
        //                                     alertify.notify(response.data["message"], 'warning', "20");
        //                                     break;
        //                                 }
        //                             }
        //                         }
        //                     }).catch(function (error) {
        //                     alertify.notify("عدم توانایی در انجام عملیات" + `(${error})`, 'warning', "20");
        //                 });
        //             }
        //             break;
        //         }
        //     }
        // });
        $(".selectpicker").selectpicker();
        let from = $(".persian_datepicker_range_from").pDatepicker({
            initialValue: false,
            initialValueType: 'persian',
            format: 'YYYY/MM/DD',
            autoClose: true,
            observer: true,
            formatter: function(unix) {
                let date = new persianDate(unix);
                let gregorian = date.toLocale('en').toCalendar('persian');
                return gregorian.format("YYYY/MM/DD");
            },
            onSelect: function (unix) {
                from.touched = true;
                if (to && to.options && to.options.minDate !== unix) {
                    var cachedValue = to.getState().selected.unixDate;
                    to.options = {minDate: new persianDate(unix).add('d', 1)};
                    if (to.touched) {
                        to.setDate(cachedValue);
                    }
                }
            }
        });
        let to = $(".persian_datepicker_range_to").pDatepicker({
            initialValue: false,
            initialValueType: 'persian',
            format: 'YYYY/MM/DD',
            autoClose: true,
            observer: true,
            formatter: function(unix) {
                let date = new persianDate(unix);
                let gregorian = date.toLocale('en').toCalendar('persian');
                return gregorian.format("YYYY/MM/DD");
            },
            onSelect: function (unix) {
                to.touched = true;
                if (from && from.options && from.options.maxDate !== unix) {
                    var cachedValue = from.getState().selected.unixDate;
                    from.options = {maxDate: new persianDate(unix).subtract('d', 1)};
                    if (from.touched) {
                        from.setDate(cachedValue);
                    }
                }
            }
        });
        $(".enable_by_click").click(function (){
            $(this).attr("readonly","");
        });
        $("input").on("input",function (){
            if ($(this).hasClass("is-invalid"))
                $(this).removeClass("is-invalid");
        });
        $("select").on("change",function (){
            if ($(this).hasClass("is-invalid"))
                $(this).removeClass("is-invalid");
        });
        $(".date_masked").each(function (){
            $(this).mask($(this).data('mask'));
        });
        $(".number_masked").each(function (){
            $(this).mask($(this).data('mask'));
        });
        if ($(".thousand_separator").length > 0)
            new AutoNumeric.multiple('.thousand_separator',['integer',{'digitGroupSeparator':',','watchExternalChanges':true}]);
        if ($(".alert-success").length){
            setTimeout(function(){
                $('.information-box').fadeOut(1000,function (){
                    $(this).remove();
                })
            }, 4000);
        }
        $("input[type='number']").change(function (){
            if ($(this).val() === "")
                $(this).val("0");
        });
        $("#contract_subset_id").trigger("change");
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        }).on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        });
    },
    computed: {
        total_extra_work() {
            let sum = 0;
            if (this.table_data_records?.performance_automation?.performances) {
                this.table_data_records.performance_automation.performances.forEach((item) => {
                    if (typeof item.employee["performance_data"] !== "undefined")
                        sum += Number(item.employee["performance_data"][1]);
                });
            } else if(this.table_data_records?.employees) {
                this.table_data_records.employees.forEach((item) => {
                    if (typeof item["performance_data"] !== "undefined")
                        sum += Number(item["performance_data"][1]);
                });
            }
            return sum;
        }
    },
    watch:{
        table_data_records: {
            handler: function () {
                const self = this;
                this.$nextTick(function () {
                    if ($("#contract_subset_id").length) {
                        switch (self.employee_list_selection) {
                            case "contract": {
                                this.filtered_data = this.$data.table_data_records.filter((item) => {
                                    return item.contract_subset_id === Number($("#contract_subset_id").val());
                                });
                                break;
                            }
                            case "group": {
                                break;
                            }
                        }
                    }
                });
            },
            deep:true
        }
    },
    methods:{
        recaptcha(e){
            e.preventDefault();
            axios.post("/recaptcha")
                .then(function (response) {
                    if (response.data !== null)
                        $('.captcha-image').html(response.data.captcha);
                }).catch(function (){
                alertify.error("خطا در ایجاد کد امنیتی!");
            });
        },
        hide_ribbon(){
            const tabs = $("header .tab-pane");
            const nav_links = $("header .nav-link");
            tabs.each(function (){
                $(this).hasClass("active") && $(this).hasClass("show") ? $(this).removeClass(["active","show"]) : null;
            });
            nav_links.each(function (){
                $(this).hasClass("active") ? $(this).removeClass("active") : null;
            });
        },
        logout(e){
            e.preventDefault();
            const self = this;
            bootbox.confirm({
                message: "آیا برای خروج از حساب کاربری اطمینان دارید؟",
                buttons: {
                    confirm: {
                        label: 'بله',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'خیر',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result === true) {
                        self.show_loading = true;
                        e.target.submit();
                    }
                }
            });
        },
        submit_form(e){
            const self = this;
            let json_data;
            e.preventDefault();
            if (typeof e.target.dataset.json !== "undefined"){
                switch (e.target.dataset.json){
                    case "employees_data":{
                        json_data = JSON.stringify(self.table_data_records);
                        break;
                    }
                    case "attributes_list":{
                        json_data = JSON.stringify(self.attributes_list)
                        break;
                    }
                    case "invoice_cover_list":{
                        json_data = JSON.stringify(self.invoice_cover_list)
                        break;
                    }
                    case "advantage_list":{
                        json_data = JSON.stringify(self.advantage_list)
                        break;
                    }
                }
                $(`#${e.target.dataset.json}`).val(json_data);
            }
            bootbox.confirm({
                message: "آیا برای ایجاد تغییرات و ذخیره سازی اطمینان دارید؟",
                buttons: {
                    confirm: {
                        label: 'بله',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'خیر',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result === true) {
                        $(".submit_button").prop("disabled",true);
                        $(".submit_button_icon").removeClass("fa-database").addClass("fa-rotate").addClass("fa-spin");
                        $(".thousand_separator").length > 0 && $(".thousand_separator").parent() === e.target ? AutoNumeric.getAutoNumericElement('.thousand_separator').formUnformat() : '';
                        self.show_loading = true;
                        e.target.submit();
                    }
                }
            });
        },
        main_route_change() {
            let main = $("#main");
            main.find('option').remove();
            $("#menu_action_id option:selected").map(function () {
                main.append(`<option value=${$(this).val()}>${$(this).text()}</option>`);

            });
            main.selectpicker('refresh');
        },
        menu_action_checkmark(e) {
            e.stopPropagation();
            $(e.currentTarget).children('input[type="checkbox"]').prop('checked',!$(e.currentTarget).children('input[type="checkbox"]').prop('checked'));
        },
        select_all_checkboxes(e){
            $(e.target).closest('ul').find('input[type="checkbox"]').prop("checked",true);
        },
        deselect_all_checkboxes(e){
            $(e.target).closest('ul').find('input[type="checkbox"]').prop("checked",false);
        },
        login(){
            $(".login-button").prop("disabled",true);
            $("#login-button-icon").removeClass("fa-sign-in").addClass("fa-circle-notch fa-spin fa-1-6x");
            $("#login-button-text").text('');
        },
        to_persian_date(date){
            return new persianDate(date).format("HH:mm:ss YYYY/MM/DD")
        },
        check_unemployed(status){
            let result;
            status === 0 ? result = "<i class='fa fa-check-circle green-color fa-1-6x'></i>" : result = "<i class='fa fa-times-circle red-color fa-1-6x'></i>";
            return result;
        },
        filter_table(e){
            let filter = e.target.value;
            let table = document.getElementById(e.target.dataset.table), columns, tr, td, i, j, txtValue;
            columns = JSON.parse(table.dataset.filter);
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                let strings = [];
                for (j = 0; j < columns.length; j++) {
                    td = tr[i].getElementsByTagName("td")[parseInt(columns[j])];
                    if (td) {
                        txtValue = td.textContent || td.innerText || td.querySelector('input').value;
                        strings.push(txtValue);
                    }
                }
                if (strings.length) {
                    const match = strings.find(element => {
                        const clearElement = element.replace(/[()\-_!@#$%^.,]/g, '');
                        return !!clearElement.includes(filter);
                    });
                    if (match)
                        tr[i].style.display = "";
                    else
                        tr[i].style.display = "none";
                }
            }
        },
        reset_employees_table(e){
            this.filtered_data = [];
            this.filtered_data = this.table_data_records.filter((item) => {
                return item.contract_subset_id === Number(e.target.value);
            });
        },
        editable_input(e){
            e.target.readOnly = false;
        },
        read_only(e){
            e.target.readOnly = true;
        },
        add_role_item(e){
            const roles = $(e.target);
            let duplicate,slug;
            if (roles.val()){
                do {
                    slug = Math.floor(Math.random() * 100) + 10;
                    duplicate = this.roles_list.find((item) => {
                        return item.slug === slug
                    });
                }
                while (typeof duplicate !== "undefined")
                this.roles_list.push({"name":roles.find('option:selected').text(),"id":roles.val(),"slug":slug});
            }
        },
        modify_role(e){
            if (this.roles_list.length > 0) {
                const index = this.roles_list.findIndex( item => {
                    return item.slug === Number(e.currentTarget.dataset.slug);
                });
                const max_index = this.roles_list.length - 1;
                if (index >= 0) {
                    switch (e.currentTarget.dataset.function) {
                        case "up": {
                            if (index > 0)
                                this.roles_list.splice(index - 1,2,this.roles_list[index],this.roles_list[index - 1]);
                            break;
                        }
                        case "down": {
                            if (index < max_index) {
                                this.roles_list.splice(index, 2,this.roles_list[index + 1],this.roles_list[index]);
                            }
                            break;
                        }
                        case "remove": {
                            this.roles_list.splice(index, 1);
                            break;
                        }
                    }
                }
            }
        },
        add_attribute_item(e){
            const attribute = $(`#${e.currentTarget.dataset.element}`).val();
            let duplicate,slug = Math.floor(Math.random() * 100) + 10;
            if (attribute !== "" && attribute !== null) {
                if (this.attributes_list.length) {
                    const same = this.attributes_list.find((item) => {
                        return item.name === attribute
                    });
                    if (typeof same === "undefined") {
                        do {
                            duplicate = this.attributes_list.find((item) => {
                                return item.slug === slug
                            });
                            slug = Math.floor(Math.random() * 100) + 10;
                        }
                        while (typeof duplicate !== "undefined")
                        this.attributes_list.push({"name": attribute, "slug": slug, "kind": "number", "category": ""});
                    }
                }
                else
                    this.attributes_list.push({"name": attribute, "slug": slug, "kind": "number", "category": ""});
            }
        },
        modify_attribute(e){
            if (this.attributes_list.length > 0) {
                const index = this.attributes_list.findIndex( item => {
                    return item.slug === Number(e.currentTarget.dataset.slug);
                });
                const max_index = this.attributes_list.length - 1;
                if (index >= 0) {
                    switch (e.currentTarget.dataset.function) {
                        case "up": {
                            if (index > 0)
                                this.attributes_list.splice(index - 1,2,this.attributes_list[index],this.attributes_list[index - 1]);
                            break;
                        }
                        case "down": {
                            if (index < max_index) {
                                this.attributes_list.splice(index, 2,this.attributes_list[index + 1],this.attributes_list[index]);
                            }
                            break;
                        }
                        case "remove": {
                            this.attributes_list.splice(index, 1);
                            break;
                        }
                    }
                }
            }
        },
        get_employee_performance_value(id,i){
            if (this.table_data_records?.performance_automation?.performances){
                let index = this.table_data_records.performance_automation.performances.map((item) => { return item.employee.id; }).indexOf(id);
                if(typeof this.table_data_records.performance_automation.performances[index].employee["performance_data"] !== "undefined"){
                    if (typeof this.table_data_records.performance_automation.performances[index].employee["performance_data"][i] !== "undefined")
                        return this.table_data_records.performance_automation.performances[index].employee["performance_data"][i] !== null ? this.table_data_records.performance_automation.performances[index].employee["performance_data"][i] : 0;
                }
            }
            else if (this.table_data_records?.employees){
                let index = this.table_data_records.employees.map((employee) => { return employee.id; }).indexOf(id);
                if (typeof this.table_data_records.employees[index]["performance_data"] !== "undefined")
                    return this.table_data_records.employees[index]["performance_data"][i] !== null ? this.table_data_records.employees[index]["performance_data"][i] : 0;
            }
            return 0;
        },
        set_employee_performance_value(e,id,i){
            let tmp = JSON.parse(JSON.stringify(this.table_data_records));
            if (this.table_data_records?.performance_automation?.performances){
                e.target.value === "" || e.target.value === null ? e.target.value = 0 : "";
                let index = this.table_data_records.performance_automation.performances.map((item) => { return item.employee.id; }).indexOf(id);
                if(typeof this.table_data_records.performance_automation.performances[index].employee["performance_data"] !== "undefined"){
                    if (typeof this.table_data_records.performance_automation.performances[index].employee["performance_data"][i] !== "undefined") {
                        tmp.performance_automation.performances[index].employee["performance_data"][i] = e.target.type === 'number' ? Number(e.target.value) : e.target.value;
                    }
                }
            }
            else if (this.table_data_records?.employees){
                e.target.value === "" || e.target.value === null ? e.target.value = 0 : "";
                let index = this.table_data_records.employees.map((employee) => { return employee.id; }).indexOf(id);
                if(typeof this.table_data_records.employees[index]["performance_data"] !== "undefined"){
                    if (typeof this.table_data_records.employees[index]["performance_data"][i] !== "undefined") {
                        tmp.employees[index]["performance_data"][i] = e.target.type === 'number' ? Number(e.target.value) : e.target.value;
                    }
                }
            }
            this.table_data_records = JSON.parse(JSON.stringify(tmp));
        },
        set_employee_self_value(e,id,i){
            let index = this.table_data_records.performance_automation.performances.map((item) => { return item.id; }).indexOf(id);
            if(this.table_data_records?.performance_automation?.performances){
                if (typeof this.table_data_records.performance_automation.performances[index] !== "undefined") {
                    this.table_data_records.performance_automation.performances[index][i] = e.target.value.replaceAll(',','');
                }
            }
        },
        performance_validation(e){
            const self = this;
            let extra_work_error = [];
            let work_day_error = [];
            switch (e.currentTarget.dataset.method){
                case "new":{
                    this.table_data_records.employees.forEach((item,index) => {
                        if (typeof item["performance_data"] !== "undefined"){
                            if(item["performance_data"][1] > self.table_data_records.overtime_registration_limit)
                                extra_work_error.push(index);
                            if(item["performance_data"][0] + item["performance_data"][10] + item["performance_data"][11] + item["performance_data"][12] === 0)
                                work_day_error.push(index);
                        }
                        else
                            work_day_error.push(index);
                    });
                    break;
                }
                case "edit":{
                    this.table_data_records.performance_automation.performances.forEach((item,index) => {
                        if (typeof item.employee["performance_data"] !== "undefined"){
                            if(item.employee["performance_data"][1] > self.table_data_records.overtime_registration_limit)
                                extra_work_error.push(index);
                            if(item.employee["performance_data"][0] + item.employee["performance_data"][10] + item.employee["performance_data"][11] + item.employee["performance_data"][12] === 0)
                                work_day_error.push(index);
                        }
                        else
                            work_day_error.push(index);
                    });
                    break;
                }
            }
            if (extra_work_error.length > 0 || work_day_error.length > 0){
                let tbody = $("#search_table").find("tbody")[0];
                if (work_day_error.length > 0) {
                    work_day_error.forEach((item) => {
                        tbody.rows[item].cells[2].children[0].classList.add("is-invalid");
                        tbody.rows[item].cells[2].children[0].setAttribute("title","مقادیر کارکرد و انواع مرخصی ها نمی تواند به طور همزمان صفر باشد");
                    });
                }
                if (extra_work_error.length > 0) {
                    extra_work_error.forEach((item) => {
                        tbody.rows[item].cells[3].children[0].classList.add("is-invalid");
                        tbody.rows[item].cells[3].children[0].setAttribute("title","میزان وارد شده از سقف مجاز مرخصی بیشتر است");
                    });
                }
                bootbox.alert({
                    "message": "لطفا مقادیر مشخص شده در جدول را اصلاح نمایید",
                    closeButton: false,
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    }
                });
            }
            else
                $("#submit_validated_form").click();
        },
        get_employee_invoice_value(id,i){
            if (this.table_data_records.invoice_automation?.invoices) {
                let index = this.table_data_records.invoice_automation.invoices.map((item) => {return item.employee.id;}).indexOf(id);
                if (typeof this.table_data_records.invoice_automation.invoices[index].employee["invoice_data"] !== "undefined") {
                    if (typeof this.table_data_records.invoice_automation.invoices[index].employee["invoice_data"][i] !== "undefined")
                        return this.table_data_records.invoice_automation.invoices[index].employee["invoice_data"][i] !== null ? this.table_data_records.invoice_automation.invoices[index].employee["invoice_data"][i] : 0;
                }
            }
            else {
                let index = this.table_data_records.performance_automation.performances.map((item) => {return item.employee.id;}).indexOf(id);
                if (typeof this.table_data_records.performance_automation.performances[index].employee["invoice_data"] !== "undefined") {
                    if (typeof this.table_data_records.performance_automation.performances[index].employee["invoice_data"][i] !== "undefined")
                        return this.table_data_records.performance_automation.performances[index].employee["invoice_data"][i] !== null ? this.table_data_records.performance_automation.performances[index].employee["invoice_data"][i] : 0;
                }
            }
            return 0;
        },
        set_employee_invoice_value(e,id,i){
            if (this.table_data_records?.invoice_automation?.invoices){
                e.target.value === "" || e.target.value === null ? e.target.value = 0 : "";
                let index = this.table_data_records.invoice_automation.invoices.map((item) => { return item.employee.id; }).indexOf(id);
                if(typeof this.table_data_records.invoice_automation.invoices[index].employee["invoice_data"] !== "undefined"){
                    if (typeof this.table_data_records.invoice_automation.invoices[index].employee["invoice_data"][i] !== "undefined") {
                        this.table_data_records.invoice_automation.invoices[index].employee["invoice_data"][i] = e.target.type === 'number' ? Number(e.target.value) : e.target.value;
                    }
                }
            }
            else {
                e.target.value === "" || e.target.value === null ? e.target.value = 0 : "";
                let index = this.table_data_records.performance_automation.performances.map((item) => { return item.employee.id; }).indexOf(id);
                if(typeof this.table_data_records.performance_automation.performances[index].employee["invoice_data"] !== "undefined"){
                    if (typeof this.table_data_records.performance_automation.performances[index].employee["invoice_data"][i] !== "undefined") {
                        this.table_data_records.performance_automation.performances[index].employee["invoice_data"][i] = e.target.type === 'number' ? Number(e.target.value) : e.target.value;
                    }
                }
            }
        },
        register_invoice_cover_titles(){
            let data = [];
            let flag = false;
            $(".cover_value_input").each(function (){
                if ($(this).val() !== null && $(this).val() !== "")
                    data.push({"id":$(this).attr("id"),"value": $(this).val().replaceAll(',', '')});
                else {
                    $(this).addClass("is-invalid");
                    flag = true;
                }
            });
            this.invoice_cover_data = data;
            if (!flag)
                $("#invoice_cover").modal('hide');
        },
        invoice_validation(){
            if (this.invoice_cover_data.length !== $(".cover_value_input").length){
                bootbox.alert({
                    "message": "لطفا مقادیر روکش وضعیت را وارد نمایید",
                    closeButton: false,
                    buttons: {
                        ok: {
                            label: 'قبول'
                        }
                    }
                });
            }
            else {
                $("#invoice_cover_data").val(JSON.stringify(this.invoice_cover_data))
                $("#submit_validated_form").click();
            }
        },
        expand_element(){
            $(".expand-element").toggleClass("disable");
            if ($(".expand-element").hasClass("disable"))
                $("#expand_button").text("بستن لیست پرسنل");
            else
                $("#expand_button").text("مشاهده لیست پرسنل");
        },
        search_table_filter(e){
            let filter = $("#contract_id").val() + " " + $("#year").val() + " " + $("#month").val();
            let table = document.getElementById(e.currentTarget.dataset.table), columns, tr, td, i, j;
            columns = JSON.parse(table.dataset.filter);
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                let strings = [];
                let txtValue;
                for (j = 0; j < columns.length; j++) {
                    td = tr[i].getElementsByTagName("td")[parseInt(columns[j])];
                    if (td)
                        txtValue += td.textContent || td.innerText || td.querySelector('input').value + " ";
                }
                strings.push(txtValue);
                if (strings.length) {
                    const match = strings.find(element => {
                        const clearElement = element.replace(/[()\-_!@#$%^.,]/g, '');
                        return !!clearElement.includes(filter);
                    });
                    if (match)
                        tr[i].style.display = "";
                    else
                        tr[i].style.display = "none";
                }
            }
            $(".modal").modal('hide');
        },
        change_attributes_kind(e,slug){
            const index = this.attributes_list.findIndex( item => {
                return item.slug === Number(slug);
            });
            if (index >= 0)
                this.attributes_list[index]["kind"] = e.target.value;
        },
        change_attributes_category(e,slug) {
            const index = this.attributes_list.findIndex(item => {
                return item.slug === Number(slug);
            });
            if (index >= 0)
                this.attributes_list[index]["category"] = e.target.value;
        },
        add_cover_item(e){
            const attribute = $(`#${e.currentTarget.dataset.element}`).val();
            let duplicate,slug = Math.floor(Math.random() * 100) + 10;
            if (attribute !== "" && attribute !== null) {
                if (this.invoice_cover_list.length) {
                    const same = this.invoice_cover_list.find((item) => {
                        return item.name === attribute
                    });
                    if (typeof same === "undefined") {
                        do {
                            duplicate = this.invoice_cover_list.find((item) => {
                                return item.slug === slug
                            });
                            slug = Math.floor(Math.random() * 100) + 10;
                        }
                        while (typeof duplicate !== "undefined")
                        this.invoice_cover_list.push({"name": attribute, "slug": slug, "kind": "number"});
                    }
                }
                else
                    this.invoice_cover_list.push({"name": attribute, "slug": slug, "kind": "number"});
            }
        },
        modify_cover(e){
            if (this.invoice_cover_list.length > 0) {
                const index = this.invoice_cover_list.findIndex( item => {
                    return item.slug === Number(e.currentTarget.dataset.slug);
                });
                const max_index = this.invoice_cover_list.length - 1;
                if (index >= 0) {
                    switch (e.currentTarget.dataset.function) {
                        case "up": {
                            if (index > 0)
                                this.invoice_cover_list.splice(index - 1,2,this.invoice_cover_list[index],this.invoice_cover_list[index - 1]);
                            break;
                        }
                        case "down": {
                            if (index < max_index) {
                                this.invoice_cover_list.splice(index, 2,this.invoice_cover_list[index + 1],this.invoice_cover_list[index]);
                            }
                            break;
                        }
                        case "remove": {
                            this.invoice_cover_list.splice(index, 1);
                            break;
                        }
                    }
                }
            }
        },
        change_invoice_cover_kind(e,slug){
            const index = this.invoice_cover_list.findIndex( item => {
                return item.slug === Number(slug);
            });
            if (index >= 0)
                this.invoice_cover_list[index]["kind"] = e.target.value;
        },
        get_cover_value(cover_id){
            if (this.invoice_cover_data.length > 0){
                const index = this.invoice_cover_data.findIndex( item => {
                    return Number(item.id) === cover_id;
                });
                if (index >= 0){
                    return this.invoice_cover_data[index]["value"];
                }
            }
        },
        add_advantage_item(e){
            const attribute = $(`#${e.currentTarget.dataset.element}`).val();
            let duplicate,slug = Math.floor(Math.random() * 100) + 10;
            if (attribute !== "" && attribute !== null) {
                if (this.advantage_list.length) {
                    const same = this.advantage_list.find((item) => {
                        return item.name === attribute
                    });
                    if (typeof same === "undefined") {
                        do {
                            duplicate = this.advantage_list.find((item) => {
                                return item.slug === slug
                            });
                            slug = Math.floor(Math.random() * 100) + 10;
                        }
                        while (typeof duplicate !== "undefined")
                        this.advantage_list.push({"name": attribute, "slug": slug, "kind": "text"});
                    }
                }
                else
                    this.advantage_list.push({"name": attribute, "slug": slug, "kind": "text"});
            }
        },
        modify_advantage(e){
            if (this.advantage_list.length > 0) {
                const index = this.advantage_list.findIndex( item => {
                    return item.slug === Number(e.currentTarget.dataset.slug);
                });
                const max_index = this.advantage_list.length - 1;
                if (index >= 0) {
                    switch (e.currentTarget.dataset.function) {
                        case "up": {
                            if (index > 0)
                                this.advantage_list.splice(index - 1,2,this.advantage_list[index],this.advantage_list[index - 1]);
                            break;
                        }
                        case "down": {
                            if (index < max_index) {
                                this.advantage_list.splice(index, 2,this.advantage_list[index + 1],this.advantage_list[index]);
                            }
                            break;
                        }
                        case "remove": {
                            this.advantage_list.splice(index, 1);
                            break;
                        }
                    }
                }
            }
        },
        change_advantage_kind(e,slug){
            const index = this.advantage_list.findIndex( item => {
                return item.slug === Number(slug);
            });
            if (index >= 0)
                this.advantage_list[index]["kind"] = e.target.value;
        },
        change_table_height(){
            $("#table-scroll").toggleClass("low-height-table").toggleClass("full-height-table");
        },
        reset_password_platform(e){
            const target = e.currentTarget;
            const mobile = $("#mobile"), email = $("#email");
            switch (target.value){
                case "mobile":{
                    if($(target).prop("checked") === true) {
                        mobile.prop("disabled", false).focus();
                        email.prop("disabled", true);
                    }
                    break;
                }
                case "email":{
                    if($(target).prop("checked") === true) {
                        mobile.prop("disabled", true);
                        email.prop("disabled", false).focus();
                    }
                    break;
                }
            }

        },
        pop_up_custom_file(e){
            $(e.target).closest('div').find('input[type="file"]').click();
        },
        custom_file_check(e){
            let valid_ext = ['png','jpg','bmp','tiff','pdf','xlsx','txt','doc','docx'];
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
                if (file_size > 325000)
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
                $(e.target).closest('div').find('input[type="text"]').val("فایلی انتخاب نشده است");
            } else
                $(e.target).closest('div').find('input[type="text"]').val(file_names.toString());
        },
        show_record_data(e){
            const self = this;
            let data_container = $("#json_data");
            data_container.html('');
            const index = this.report_json_data.findIndex( item => {
                return Number(item.id) === Number(e.currentTarget.dataset.id);
            });
            if (index >= 0){
                const data = JSON.parse(self.report_json_data[index]["data"]);
                const keys = Object.keys(data);
                keys.forEach(function (item){
                    data_container.append(`<div class="form-group col-3">
                            <label class="col-form-label">مشخصه ${item}</label>
                            <input class="form-control iranyekan" readonly value="${data[item]}">
                        </div>`)
                })
            }
        }
    }
});
