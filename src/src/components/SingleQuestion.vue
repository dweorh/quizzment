<template>
    <section class="question-block">
        <div>
            <video v-if="videoQuestion" controls class="question-media">
                <source :src="videoUrl" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div v-else :style="'background-image: url(\'' + imageUrl + '\')'" class="question-media" />
        </div>
        <div class="text-xl">
            {{props.question.q_PL}} ({{props.question.points}}pkt.)
        </div>
        <section v-if="yesNoQuestion" class="text-lg mt-1 mb-1 cursor-pointer">
            <div @click="answer = 'T'" class="mb-2 border-2 p-2 border-gray-100 hover:bg-zinc-200">
                <input type="radio" v-model="answer" value="T">
                <span class="ml-2">Tak</span>
            </div>
            <div @click="answer = 'N'" class="border-2 p-2 border-gray-100 hover:bg-zinc-200">
                <input type="radio" v-model="answer" value="N">
                <span class="ml-2">Nie</span>
            </div>
        </section>
        <section v-else>
            <div @click="answer = 'A'" class="mb-2 border-2 p-2 border-gray-100 hover:bg-zinc-200">
                <input type="radio" v-model="answer" value="A">
                <span class="ml-2">{{props.question.a_A_PL}}</span>
            </div>
            <div @click="answer = 'B'" class="mb-2 border-2 p-2 border-gray-100 hover:bg-zinc-200">
                <input type="radio" v-model="answer" value="B">
                <span class="ml-2">{{props.question.a_B_PL}}</span>
            </div>
            <div @click="answer = 'C'" class="border-2 p-2 border-gray-100 hover:bg-zinc-200">
                <input type="radio" v-model="answer" value="C">
                <span class="ml-2">{{props.question.a_C_PL}}</span>
            </div>
        </section>
        <div class="text-right mt-4">
            <span @click="$emit('gotAnswer', answer)" class="p-2 border-2 border-gray-100 hover:bg-zinc-200 cursor-pointer">Dalej</span>
        </div>
    </section>
</template>

<script setup lang="ts">
import { watch } from "vue"
import { ref, computed } from "@vue/reactivity"
import { ROOT_URL } from "@/stores/session"
const ROOT_MEDIA_URL = ROOT_URL + '/media-for-web/'

const props = defineProps<{
  question: object
}>()

defineEmits(['gotAnswer'])

const yesNoQuestion = computed(() => props.question.a === 'T' || props.question.a === 'N')
const videoQuestion = computed(() => props.question.media.substring(props.question.media.length - 3) === 'wmv')
const videoUrl = computed(() => ROOT_MEDIA_URL + props.question.media.substring(0, props.question.media.length - 3) + 'm4v' )
const imageUrl = computed(() => ROOT_MEDIA_URL + (props.question.media ? props.question.media : 'brak_zdjecia_1024x576.jpg'))

const answer = ref('')

watch(props.question, (newQ, oldQ) => {
    if (!oldQ || newQ.id != oldQ.id) {
        answer.value = ''
    }
})
</script>

<style scoped>

</style>