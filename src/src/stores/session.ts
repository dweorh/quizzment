import { defineStore, type Store } from 'pinia';
import type { Lesson } from '@/types/lesson';
import axios from 'axios';

const serializeSession = (store : Store) : void => {
    localStorage.setItem('session', JSON.stringify(store.$state));
    // console.log('[serialize session]', JSON.stringify(store.$state));
}

export const loadSessionStore = (store: Store<"category">) : void => {
    const session = localStorage.getItem('session');
    if (session) {
        const state = JSON.parse(session);
        // const _store = store()
        store.setId(state.id);
        store.setCategory(state.cat);
        store.setLesson(state.lesson);
        // console.log('[loaded session]', state, store);
    }
}
export const ROOT_URL = 'https://www.e-prawojazdy.com'
export const API_URL = ROOT_URL + '/api.php'

// axios.defaults.baseURL = ROOT_URL;
axios.defaults.headers.common['Content-Type'] ='application/json;charset=utf-8';
// axios.defaults.headers.post['Access-Control-Allow-Origin'] = '*';
export const useSessionStore = defineStore({
  id: 'category',
  state: () => ({
    id: "",
    cat: "",
    lesson: {}
  }),
  getters: {
    category: (state) => state.cat
  },
  actions: {
    newId() {
      axios.get(API_URL + '?action=new_session').then(res => {
        if (res && res.data && res.data.id) {
          this.setId(res.data.id)
        }
      })
    },
    setId(id: string) {
      this.id = id
      serializeSession(this)
    },
    setCategory(cat : string) {
      this.cat = cat
      serializeSession(this)
    },
    setLesson(lesson : Lesson) {
      this.lesson = lesson
      serializeSession(this)
    }
  }
})

// unserializeSession(useSessionStore())