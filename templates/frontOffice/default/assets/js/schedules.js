$('#tabschedules').on('shown.bs.tab', function () {
    var calendarEl = document.getElementById('fullcalendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: creneaux,
        displayEventEnd: true,
    });
    calendar.render();

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