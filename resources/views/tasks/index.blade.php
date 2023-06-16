<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel='stylesheet' href="css/calendar.css"/>
    <link rel="stylesheet" href="css/style.css">

    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js'></script>

    <style>
        #calendar{
            width: 800px;
        }
    </style>
</head>
<body>

<section class="title-bar">
    <span class="title-bar__year">
      Calendar
    </span>
</section>

<main class="calendar-contain">
<aside class="calendar__sidebar">
    <div class="sidebar__nav">
      <!-- Icons by Icons8 -->
      <span class="sidebar__nav-item"><img class="icon icons8-Plus-Math" width="22px" height="22px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAWUlEQVRYR+2WMQoAIAgA9f+PrsWmEMQSEa7Z8rzEUmle2pxfABhvYFkPpQtJb7TEAGAAAxgoM3AO/v1YXoPPm4TtANHKy64AAAxgAANjDERB3bjXXzEA8w1s3k4WIU0YaEoAAAAASUVORK5CYII="></span>
      <span class="sidebar__nav-item"><img class="icon icons8-Share" width="22px" height="22px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAB+klEQVRYR+2W0VEdMQxFDxUQKoBUkFABUAFQAaGDUEFCBQkVABUAFRAqgFRAUgFQQTJnxprx2/V67f3h5+nnzfNK8vXVteQN3tk23nl/1gCWMrAD/AD2UwlvgTPgtbekSwB8AJ4Bf3Nz84+9IJYAuAJOgAfgKGNgD7gGvvSwsATAH2Ab2MpOKxsvgN9kodl6AHha6279tRyAa5ZFE4R6UBez1gogaM8T/gKO08JNJsjw+ZmAVEG0AMg3Pwf8/wRsDjK/AZ+TBr6lbxfA1xqCOQDS7um0A8BTa1L+fSBC/0u/lsfJ0mQ55gCE4KyplPaYJ1czVWHWAHidLoG/mfB6AIQgvTGnqXSj+BoAaTusBTegCRbusnKthNUABP157Rv2XHGxVd/XylAD8C+lmtNJDVQ0KH2KeaaSDxuL104hhsrnmIhh5bWMxmWHHMWXABjwODFsdhtAdMWXANSGzaSYMkpCvE3DqgQgar902DiW7ZKl+JEWSgBKCUITttvhO2Coh1r8qKeUAASFpWHTMu+jhKX4UQmnRFgaNp7Ufu9Aqpk+MYxyP9mTyZVnW+0a2vu9RrZSBeWLJ9qr31wTqKaf3+18ce0iRtr1s7WP3ow9TcauJr0CqpkbullMzqpzD4BIZHLBeOpPafF3OqWbCrLZlgBoTt7iuAawZuA/xAh3Ifk/Dm0AAAAASUVORK5CYII="></span>
      <span class="sidebar__nav-item"><img class="icon icons8-Up" width="22px" height="22px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAxklEQVRYR+3VwQ2DMAyF4Z8NOkI36AodpZ2sjMAoHaWdoJWlIEXI4RHnwMWROAH2ZxuSiZPXdHJ+EjDagUcZ4Rwd5QjAkr9K4icQQkQBdfK1+BAiAvCShxG9gG3b6xGExtED8Gb+K6VbnNA3cRTQCl4DzNKNOALYC7oFdCMUQFXkAboQCvAGbkDrF2sBasQXuAIfb7NSgAtwB5bGTrcHWBFWhF3uUgC1wyqAen/4NExAdiA7kB3IDmQH5GGjHhg9DVV8eT8Bf2HqNiEP+isaAAAAAElFTkSuQmCC"></span>
      <span class="sidebar__nav-item"><img class="icon icons8-Down" width="22px" height="22px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAA4UlEQVRYR+2WgQnCMBBFXzdwBSdwBUfRyXQER3EUN1AOEgnxLrkkhYCkUFpI7/+Xf1fajcnHNtmfBTCawDu0sFunuzAYL4CVwEpgJbAS+P8EDsAZeBj/DbUELsAznKpE7WMkxSfgCtwVhRKAmN+AF3AM1x+JGkAUkUINwgKo1X1BagDyYElMA3Cbi7gHoASRAzSZtwBYEClAs3krgAYhQxbnI73XBrbrLdCK0p3m69bbYv79e2cgF9Agms17WpCCdPU830lvAlFHIORw93xvALO33oXRBLw+uw/hsHEUmJ7ABzErNiGyzfJcAAAAAElFTkSuQmCC"></span>
    </div>
    <div class="sidebar__heading">
        <p style="font-size: 30px; font-weight: bold; margin-bottom:0" id="today"></p>
        <p style="margin-top: 0;" id="time"></p>
    </div>
    <ul class="sidebar__list">
      <li class="sidebar__list-item sidebar__list-item--complete"><span class="list-item__time">8.30</span> Team Meeting</li>
      <li class="sidebar__list-item sidebar__list-item--complete"><span class="list-item__time">10.00</span> Lunch with Sasha</li>
      <li class="sidebar__list-item"><span class="list-item__time">2.30</span> Design Review</li>
      <li class="sidebar__list-item"><span class="list-item__time">4.00</span> Get Groceries</li>
    </ul>
  </aside>

  <section class="calendar__days">
        <div id='calendar'></div>
  </section>
</main>
<script>
    $(document).ready(function() {
        // page is now ready, initialize the calendar...
        $('#calendar').fullCalendar({
            // put your options and callbacks here
            events : [
                @foreach($tasks as $task)
                {
                    title : '{{ $task->nama_hari_raya }}',
                    start : '{{ $task->tanggal }}',
                    url : '{{ route('tasks.edit', $task->id) }}'
                },
                @endforeach
            ]
        })
        
    });
    
    function datetime(){
        var date = new Date();
        var options = { weekday: 'long' };
        var currentDay = date.toDateString('id-ID', options);
        var currentTime = date.toLocaleTimeString('id-ID');
    
        // Mengambil elemen dengan ID "current-day"
        var currentDayElement = document.getElementById("today");
        // Mengatur teks konten dari elemen dengan nilai currentDay
        currentDayElement.textContent = currentDay;
    
        // Mengambil elemen dengan ID "current-time"
        var currentTimeElement = document.getElementById("time");
        // Mengatur teks konten dari elemen dengan nilai currentTime
        currentTimeElement.textContent = currentTime;
    }

    setInterval(datetime, 100);

</script>
</body>
</html>