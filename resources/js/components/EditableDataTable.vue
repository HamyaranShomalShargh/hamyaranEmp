<template>
    <div :class="container_class">
        <table :class="table_class">
            <thead :class="thead_class">
            <tr>
                <th v-for="(column, index) in head_columns" :key="index"> {{column}}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in table_data_records" :key='row.id'>
                <td v-for="column in columns">
                    <span v-if="Array.isArray(column)">{{relation(row,column)}}</span>
                    <span v-else>{{row[column]}}</span>
                </td>
                <td>
                    <axios-button :class="'btn btn-sm btn-outline-primary'" route="" :elements="['name','national_code','mobile']" :action="'edit'" :message="'آیا برای ویرایش اطلاعات اطمینان دارید؟'" :record_id="0">
                    <i class="fa fa-edit"></i>
                    <span class="iranyekan">ویرایش</span>
                    </axios-button>
                    <axios-button :class="'btn btn-sm btn-outline-danger'" route="" :action="'delete'" :message="'آیا برای حذف اطلاعات اطمینان دارید؟'" :record_id="0">
                    <i class="fa fa-edit"></i>
                    <span class="iranyekan">حذف</span>
                    </axios-button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "EditableDataTable",
    props: ["container_class","table_class","thead_class","head_columns","columns","edit_route","delete_route"],
    mounted() {
    },
    methods:{
      relation(row,columns) {
          let keys = columns;
          let len = keys.length;
          for(let i = 0; i < len-1; i++) {
              let key = keys[i];
              if( !row[key] ) row[key] = {}
              row = row[key];
          }
          return row[keys[len-1]];
      }
    },
    data(){
        return{
            table_data_records : typeof table_data !== "undefined" ? table_data : [],
        };
    }
}
</script>

<style scoped>
.editable-data-table th,.data-table td{
    text-align: center;
}
.editable-data-table th:first-child{
    width: 50px;
}
</style>
