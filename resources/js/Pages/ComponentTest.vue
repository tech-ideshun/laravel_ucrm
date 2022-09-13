<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';    // パス指定は[@/]でresource/js配下を指定していることになる&拡張子までつけないとエラーになる
import Label from '@/Components/InputLabel.vue';
import Input from '@/Components/TextInput.vue';

const emitTest = e => console.log(e)

</script>

<template>
    <GuestLayout>
        <Label value="件名">タイトル</Label>
        <Input 
        modelValue="初期値が入ります"
        @update:modelValue="emitTest"
        ></Input>
    </GuestLayout>
</template>