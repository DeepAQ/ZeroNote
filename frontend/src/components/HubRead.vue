<template>
  <div id="page">
    <div class="title">
      {{ note.title }}
    </div>
    <div class="info">
      <i class="el-icon-time"></i> {{ (new Date(note.updated * 1000)).toLocaleString() }}&nbsp;&nbsp;
      <i class="el-icon-star-on"></i> {{ note.upvotes }}
    </div>
    <div class="content" v-html="rendered"></div>
  </div>
</template>

<script>
import api from '../utils/api'
import MarkdownIt from 'markdown-it'

const md = new MarkdownIt()

export default {
  props: ['id'],
  data () {
    return {
      note: {}
    }
  },
  computed: {
    rendered () {
      return md.render(this.note.content)
    }
  },
  watch: {
    id () {
      this.load()
    }
  },
  methods: {
    load () {
      api('note/single', {
        id: this.id
      }).then(data => {
        this.note = data
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: reason
        })
      })
    }
  },
  mounted () {
    this.load()
  }
}
</script>

<style lang="less" scoped>
#page {
  width: 100%;
  
  .title {
    font-size: 28px;
    color: #6e0f6d;
  }

  .info {
    font-size: 14px;
    color: #666;
  }
}
</style>

<style lang="less">
.content img {
  max-width: 100%;
}
</style>
