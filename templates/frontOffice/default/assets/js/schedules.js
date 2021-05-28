$('#tabschedules').on('shown.bs.tab', function () {
    var calendarEl = document.getElementById('fullcalendar-availabilities');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: creneaux,
        displayEventEnd: true,
    });
    calendar.render();

});

$('#schedules-dates-modal').on('shown.bs.modal', function () {
    var calendarEl = document.getElementById('fullcalendar-pick-date');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: creneaux,
        displayEventEnd: true,
        eventDidMount: function(info) {
            //dans cette fonction on peut modifier le html de l'event
            info.el.dataset.dismiss = 'modal';
            info.el.dataset.id = info.event.id;
            info.el.style.cursor = 'pointer';
        },
    });
    calendar.render();

    document.querySelectorAll('#schedules-dates-modal .fc-event').forEach((el) => {
        el.addEventListener('click', (event) => {
            //event.stopPropagation();
            const dateInput = document.querySelector('#truc');
            dateInput.value = event.currentTarget.dataset.id;
        });
    });
});



/*$('#tabschedules').on('shown.bs.tab', function () {

    var calendarEl = document.getElementById('calendar');

      var calendar = new Calendar(calendarEl, {
          plugins: [ dayGridPlugin ],
          initialView: 'dayGridMonth',
          locale: frLocale,
          events: creneaux,  //la variable creneaux a été construite dans le fichier de template
          eventDidMount: function(info) {
            //dans cette fonction on peut modifier le rendu du creneau
          },
          eventTimeFormat: {
            //ici on modifie comment s'affiche l'heure des créneaux
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
          },
          validRange: function(nowDate) {
            return {
              start: nowDate,
            };
          }
      });

    calendar.render();*/