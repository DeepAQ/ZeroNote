<template>
  <div class="note-search">
    <el-autocomplete
      v-model="kw"
      placeholder="Search ..."
      :debounce="1000"
      prefix-icon="el-icon-search"
      :fetch-suggestions="doSearch"
      v-on:select="selectResult">
      <slot name="append" slot="append"></slot>
      <template slot-scope="props">
        <div v-if="props.item.type == 'notebooks'">笔记本 | {{ props.item.name }}</div>
        <div v-if="props.item.type == 'notes'">笔记 | {{ props.item.title }}</div>
      </template>
    </el-autocomplete>
  </div>
</template>

<script>
import api from '../utils/api'

export default {
  data () {
    return {
      kw: ''
    }
  },
  methods: {
    doSearch (qs, cb) {
      if (this.kw == '') {
        cb([])
        return
      }
      api('search/aggregate', {
        kw: this.kw
      }).then(data => {
        let processed = []
        Object.keys(data).forEach(type => {
          data[type].forEach(x => {
            x['type'] = type
            processed.push(x)
          })
        })
        cb(processed)
      }).catch(reason => {})
    },
    selectResult (item) {
      switch (item.type) {
        case 'notebooks':
          this.$emit('nb-selected', item.id)
          break
        case 'notes':
          this.$router.push(`/note/${item.nbid}/${item.id}`)
          break
      }
    }
  }
}
</script>

<style lang="less">
.el-autocomplete-suggestion {
  width: 250px !important;
}
</style>
