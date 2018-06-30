<template>
  <div id="page">
    <div class="header">
      <div>
        <div class="title">
          {{ note.title }}
        </div>
        <div class="info">
          <i class="el-icon-time"></i> {{ (new Date(note.updated * 1000)).toLocaleString() }}&nbsp;&nbsp;
          <!-- <i class="el-icon-star-on"></i> {{ note.upvotes }} -->
        </div>
      </div>
      <div>
        <el-button type="primary" v-on:click="upDownVote">
          <i class="el-icon-star-on" v-if="note.upvoted"></i>
          <i class="el-icon-star-off" v-else></i>
          {{ note.upvotes }}
        </el-button>
      </div>
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
    },
    upDownVote () {
      api('note/updownvote', {
        noteid: this.id
      }).then(data => {
        this.note.upvotes = data.upvotes
        this.note.upvoted = data.upvoted
      }).catch(reason => {
        this.$message({
          showClose: true,
          type: 'error',
          message: `Upload failed: ${reason}`
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
  
  .header {
    display: flex;

    >div:first-child {
      flex: 1;
    }

    .title {
      font-size: 28px;
      color: #6e0f6d;
    }

    .info {
      font-size: 14px;
      color: #666;
    }
  }
}
</style>

<style lang="less">
.content img {
  max-width: 100%;
}
</style>
