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
            {{props.answer.question.q_PL}} ({{props.answer.question.points}}pkt.)
        </div>
        <section v-if="yesNoQuestion" class="text-lg mt-1 mb-1 cursor-pointer">
            <div readonly class="mb-2 border-2 p-2 border-gray-100"
                :class="{
                    'bg-green-400': props.answer.question.a === 'T',
                    'bg-red-400': (props.answer.question.a !== given_answer && given_answer === 'T')
                }">
                <input type="radio" v-model="given_answer" value="T">
                <span class="ml-2">Tak</span>
            </div>
            <div readonly class="border-2 p-2 border-gray-100"
                :class="{
                    'bg-green-400': props.answer.question.a === 'N',
                    'bg-red-400': (props.answer.question.a !== given_answer && given_answer === 'N')
            }">
                <input type="radio" v-model="given_answer" value="N">
                <span class="ml-2">Nie</span>
            </div>
        </section>
        <section v-else>
            <div class="mb-2 border-2 p-2 border-gray-100 "
                :class="{
                    'bg-green-400': props.answer.question.a === 'A',
                    'bg-red-400': (props.answer.question.a !== given_answer && given_answer === 'A')
                }">
                <input type="radio" v-model="given_answer" value="A">
                <span class="ml-2">{{props.answer.question.a_A_PL}}</span>
            </div>
            <div class="mb-2 border-2 p-2 border-gray-100"
                :class="{
                    'bg-green-400': props.answer.question.a === 'B',
                    'bg-red-400': (props.answer.question.a !== given_answer && given_answer === 'B')
                }">
                <input type="radio" v-model="given_answer" value="B">
                <span class="ml-2">{{props.answer.question.a_B_PL}}</span>
            </div>
            <div class="border-2 p-2 border-gray-100"
                :class="{
                    'bg-green-400': props.answer.question.a === 'C',
                    'bg-red-400': (props.answer.question.a !== given_answer && given_answer === 'C')
                }">
                <input type="radio" v-model="given_answer" value="C">
                <span class="ml-2">{{props.answer.question.a_C_PL}}</span>
            </div>
        </section>
    </section>
</template>

<script setup lang="ts">
import { watch } from "vue"
import { ref, computed } from "@vue/reactivity"
import { ROOT_URL } from "@/stores/session"

const ROOT_MEDIA_URL = ROOT_URL + '/media-for-web/'

const props = defineProps<{
  answer: object
}>()

defineEmits(['gotAnswer'])

const yesNoQuestion = computed(() => props.answer.question.a === 'T' || props.answer.question.a === 'N')
const videoQuestion = computed(() => props.answer.question.media.substring(props.answer.question.media.length - 3) === 'wmv')
const videoUrl = computed(() => ROOT_MEDIA_URL + props.answer.question.media.substring(0, props.answer.question.media.length - 3) + 'm4v' )
const imageUrl = computed(() => ROOT_MEDIA_URL + (props.answer.question.media ? props.answer.question.media : 'brak_zdjecia_1024x576.jpg'))

const given_answer = ref(props.answer.answer)

watch(props.answer, (newA) => {
    given_answer.value = newA.answer
})
</script>

<style scoped>
</style>