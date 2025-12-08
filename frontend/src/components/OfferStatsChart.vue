<script setup lang="ts">
  import { ref, onMounted, watch } from 'vue';
  import { Bar, Doughnut } from 'vue-chartjs';
  import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement,
  } from 'chart.js';

  ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement
  );

  interface Props {
    stats: {
      inPreparation: number;
      sent: number;
      accepted: number;
      rejected: number;
    };
    wonValue: number;
    lostValue: number;
  }

  const props = defineProps<Props>();

  const barChartData = ref({
    labels: ['W przygotowaniu', 'Wysłane', 'Zaakceptowane', 'Odrzucone'],
    datasets: [
      {
        label: 'Liczba ofert',
        backgroundColor: ['#9CA3AF', '#3B82F6', '#10B981', '#EF4444'],
        data: [
          props.stats.inPreparation,
          props.stats.sent,
          props.stats.accepted,
          props.stats.rejected,
        ],
      },
    ],
  });

  const barChartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false,
      },
      title: {
        display: true,
        text: 'Liczba ofert według statusu',
        font: {
          size: 16,
        },
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
        },
      },
    },
  });

  const doughnutChartData = ref({
    labels: ['Wygrane', 'Przegrane'],
    datasets: [
      {
        backgroundColor: ['#10B981', '#EF4444'],
        data: [props.wonValue, props.lostValue],
      },
    ],
  });

  const doughnutChartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom' as const,
      },
      title: {
        display: true,
        text: 'Wartość wygranych vs przegranych ofert',
        font: {
          size: 16,
        },
      },
      tooltip: {
        callbacks: {
          label: function (context: any) {
            const label = context.label || '';
            const value = context.raw || 0;
            return `${label}: ${new Intl.NumberFormat('pl-PL', {
              style: 'currency',
              currency: 'PLN',
            }).format(value)}`;
          },
        },
      },
    },
  });

  watch(
    () => [props.stats, props.wonValue, props.lostValue],
    () => {
      barChartData.value = {
        labels: ['W przygotowaniu', 'Wysłane', 'Zaakceptowane', 'Odrzucone'],
        datasets: [
          {
            label: 'Liczba ofert',
            backgroundColor: ['#9CA3AF', '#3B82F6', '#10B981', '#EF4444'],
            data: [
              props.stats.inPreparation,
              props.stats.sent,
              props.stats.accepted,
              props.stats.rejected,
            ],
          },
        ],
      };

      doughnutChartData.value = {
        labels: ['Wygrane', 'Przegrane'],
        datasets: [
          {
            backgroundColor: ['#10B981', '#EF4444'],
            data: [props.wonValue, props.lostValue],
          },
        ],
      };
    },
    { deep: true }
  );
</script>

<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Wykres słupkowy - liczba ofert -->
    <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
      <div style="height: 300px">
        <Bar :data="barChartData" :options="barChartOptions" />
      </div>
    </div>

    <!-- Wykres kołowy - wartość wygranych vs przegranych -->
    <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
      <div style="height: 300px">
        <Doughnut :data="doughnutChartData" :options="doughnutChartOptions" />
      </div>
    </div>
  </div>
</template>

<style scoped></style>

