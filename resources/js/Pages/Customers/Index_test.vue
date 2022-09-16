<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/inertia-vue3';   // import先が一緒だとカンマ区切りでimport出来る
    import FlashMessage from '@/Components/FlashMessage.vue';   
    import { onMounted } from 'vue';
    import Pagination from '@/Components/Pagination.vue';

    defineProps({   // コントローラーから渡ってきた値をキャッチ
        customers : Object   // 複数のデータ群なので型はArray型、単一だとObject型です
    })
    // const props = defineProps({   // onMountedで確認するならこっち
    //     customers : Object  
    // })

    // onMounted(() => {
    //     console.log(props.customers)
    //     console.log(props.customers.last_page)
    // })
    </script>
    
    <template>
        <ul v-for="customer in customers.data" :key="customer.id">
            <li>{{ customer.id }}</li>
            <li>{{ customer.name }}</li>
        </ul>
        <Pagination class="mt-6" :links="customers.links"></Pagination>
    </template>
    