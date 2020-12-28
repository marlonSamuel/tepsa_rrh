<script>
import { Bar } from 'vue-chartjs'
import moment from 'moment'
export default {
    extends: Bar,
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
          text: 'Resumen de pagos empleados eventuales '+moment().year()
        },
        legend: {
            display: false
         },
         tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              return 'Total: ' + self.$store.state.global.formatPrice(tooltipItem.value);
            }
          }
        },
        scales: {
          yAxes: [{
              ticks: {
                  beginAtZero: true,
                  // Include a dollar sign in the ticks
                  callback: function(value, index, values) {
                      return 'Q./ ' + value;
                  }
              }
          }]
       }
     }

     events.$on('eventual_chart',self.onEventPagos)
    },

    beforeDestroy(){
        let self = this
        events.$off('eventual_chart',self.onEventPagos)
    },

    methods: {
      onEventPagos(data){
          let self = this
          self.data = data
          self.loadData(data)
      },

      loadData(data) {
          let self = this
          data.forEach(item => {
              self.chartData.labels.push(item.numero)
              self.dataset.data.push(item.total_planilla)
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