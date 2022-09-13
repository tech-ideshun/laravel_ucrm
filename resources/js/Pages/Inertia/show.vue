<script setup>
import { Inertia } from '@inertiajs/inertia'    // Inertia.jsから[Inertia]メソッドが使える

defineProps({
    id : String,
    blog : Object
})

// const deleteConfirm = (id) => {  // アロー関数で定義する際、引数が一つだとカッコなしでもいい
//     console.log(id)
// }

const deleteConfirm = id => {
    // console.log(id)
    Inertia.delete(`/inertia/${id}`, {
        onBefore: () => confirm('本当に削除しますか?')
    })
}
</script>

<template>
    {{ id }}<br>
    {{ blog.title }}
    <button @click="deleteConfirm(blog.id)">削除</button>
</template>