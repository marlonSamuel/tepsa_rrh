<script>
import { Pie } from 'vue-chartjs'
import moment from 'moment'
export default {
    extends: Pie,
    data(){
      return {
        data: null,
        dataset: {
          label: 'Planillas '+new Date().getFullYear(),
          backgroundColor: '#f39c12',
          borderWidth: 1,
          data: [],
          backgroundColor: []
        },
        chartData: {
          labels: [], 
          datasets: []
        },
        options: {}
      }
    },
    created(){
      let self = this
     self.options = {
        responsive: true,
        maintainAspectRatio: false,
        title: {
          display: true,
          text: 'Dias de descarga planillas '+moment().year()
        },
        legend: {
            position: 'left'
        },
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              var dataLabel = data.labels[tooltipItem.index];
              var value =  data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
              return dataLabel+': '+value + ' dias';
            }
          }
        },
     }

     events.$on('eventual_days_chart',self.onEventDays)
    },

    beforeDestroy(){
        let self = this
        events.$off('eventual_days_chart',self.onEventDays)
    },

    methods: {
      onEventDays(data){
          let self = this
          self.data = data
          self.loadData(data)
      },

      loadData(data) {
          let self = this
          data.forEach(item => {
              self.chartData.labels.push(item.numero)
              self.dataset.data.push(item.diff)
              self.dataset.backgroundColor.push(self.getRandomColor())
          })

          self.chartData.datasets.push(self.dataset)
          self.renderChart(self.chartData, self.options)
      },

        getRandomColor() {
            var letters = '0123456789ABCDEF'.split('');
            var color = '#';
            for (var i = 0; i < 6; i++ ) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
    },

    mounted () {
      let self = this
      //this.renderChart(self.chartData, self.options)
    }
  }
</script>