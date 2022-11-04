<template>
    <div class="table-responsive p-3">
        <table class="table table-striped table-hover data-table">
            <thead>
            <tr>
                <th scope="col"><span class="iranyekan">شماره</span></th>
                <th scope="col"><span class="iranyekan">نام</span></th>
                <th scope="col"><span class="iranyekan">نام کاربری</span></th>
                <th scope="col"><span class="iranyekan">وضعیت</span></th>
            </tr>
            </thead>
        </table>
    </div>
</template>

<script>
export default {
    name: "DataTable"
}
</script>

<style scoped>
.data-table th,.data-table td{
    text-align: center;
}
.data-table th:first-child{
    width: 90px;
}
</style>
