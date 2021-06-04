class ScheduleDate
{
    constructor()
    {
        this.scheduleDateSelect = document.querySelector('#schedule_date');
    }

    init()
    {
        this.onTabShownCalendar();
        this.onModalShownCalendar();
        this.onScheduleDateSelectChange();
        this.setMaxQuantity();
    }

    onTabShownCalendar()
    {
        $('#tabschedules').on('shown.bs.tab', () => {
            var calendarEl = document.getElementById('fullcalendar-availabilities');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                //events: creneaux,
                displayEventEnd: true,
                events: {
                    url: '/module/schedules/calendar/events',
                    extraParams: {
                        productId: productId
                    }
                }
            });
            calendar.render();
        });
    }

    onModalShownCalendar()
    {
        $('#schedules-dates-modal').on('shown.bs.modal', () => {
            var calendarEl = document.getElementById('fullcalendar-pick-date');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                //events: creneaux,
                displayEventEnd: true,
                eventDidMount: (info) => {
                    //dans cette fonction on peut modifier le html de l'event
                    info.el.dataset.id = info.event.id;
                    if (info.event.extendedProps.selectable) {
                        info.el.style.cursor = 'pointer';
                        info.el.dataset.dismiss = 'modal';
                    } else {
                        info.el.style.pointerEvents = 'none';
                    }
                    // click on calendar events changes value of schedule date <select>
                    document.querySelectorAll('#schedules-dates-modal .fc-event').forEach((el) => {
                        el.addEventListener('click', (event) => {
                            const dateInput = document.querySelector('#schedule_date');
                            dateInput.value = event.currentTarget.dataset.id;
                            this.setMaxQuantity();
                        });
                    });
                },
                events: {
                    url: '/module/schedules/calendar/events',
                    extraParams: {
                        productId: productId
                    }
                },
                loading: function(isloading) {
                    //useful for displating a loader
                }
            });
            calendar.render();
        });
    }

    onScheduleDateSelectChange()
    {
        document.querySelector('#schedule_date').addEventListener('change', () => {
            this.setMaxQuantity();
        });
    }

    setMaxQuantity()
    {
        let selectedOption = this.scheduleDateSelect.options[this.scheduleDateSelect.selectedIndex];
        document.querySelector('#quantity').setAttribute('max', selectedOption.dataset.stock);
    }
}

(new ScheduleDate).init();
