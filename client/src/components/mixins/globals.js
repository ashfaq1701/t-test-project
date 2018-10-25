import moment from 'moment'

export const globals = {
  data () {
    return {}
  },
  methods: {
    formatMoney: function (num) {
      num = parseInt(num)
      let p = num.toFixed(2).split('.')
      let full = p[0].split('').reverse().reduce(function (acc, num, i, orig) {
        return num === '-' ? acc : num + (i && !(i % 3) ? ',' : '') + acc
      }, '')
      if (parseInt(p[1]) !== 0) {
        full = full + '.' + p[1]
      }
      return full
    },
    formatDate: function (date) {
      return moment(date).format('YYYY-MM-DD')
    }
  }
}
