<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assessment->guide?->skill_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Arial&display=swap');
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        .page { background: white; width: 21cm; min-height: 29.7cm; padding: 2cm; margin: 1cm auto; border: 1px #D3D3D3 solid; box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); box-sizing: border-box; position: relative; display: flex; flex-direction: column; }
        header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #FDB913; letter-spacing: 4px; }
        .co-op { display: inline-block; font-size: 24px; margin-left: 4px; color: black; }
        .doc-title { text-align: right; font-weight: bold; font-size: 14px; max-width: 200px; }
        .skill-section { text-align: center; margin-bottom: 20px; }
        .skill-title { font-size: 18px; font-weight: bold; text-decoration: underline; margin-bottom: 18px }
        .skill-number { font-size: 14px; font-weight: bold; text-align: left; margin-bottom: 24px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; margin-bottom: 20px; }
        .info-grid div { border-bottom: 1px dotted black; padding-bottom: 5px; min-height: 20px; }
        .task-section { margin-bottom: 20px; }
        .task-section p { margin: 10px 0; word-break: break-word }
        .task-section ol, .task-section ul { padding-left: 20px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        th, td { border: 0.5px solid black; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #00b050; font-weight: bolder; }
        td.category { background-color: #92d051; font-weight: bolder; }
        footer { margin-top: auto; padding-top: 20px; display: flex; justify-content: space-between; align-items: center; font-size: 12px; }
        .comments-section { margin-top: 20px; }
        .comments-box { border: 0.5px solid black; height: 150px; margin-top: 5px; padding: 5px; }
        .result-section { margin-top: 20px; display: flex; align-items: center; }
        .result-title { font-weight: bold; font-size: 16px; margin-right: 20px; }
        .result-options { display: flex; gap: 20px; }
        .result-options div { display: flex; align-items: center; gap: 5px; }
        .checkbox { width: 20px; height: 20px; border: 0.5px solid black; display: flex; justify-content: center; align-items: center; }
        .result-note { margin-left: auto; font-style: italic; font-size: 12px; }
        .signature-section { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 40px; margin-top: 30px; width: 100%; }
        .signature-block p { margin: 0 0 10px 0; }
        .signature-block .line { border-bottom: 1px dotted black; margin-top: 20px; position: relative; height: 30px; }
        .signature-block .line .value { position: absolute; bottom: 5px; left: 50px; }
        .signature-block .line .label { position: absolute; bottom: 5px; left: 0; font-size: 12px; font-weight: normal; }
        .text-center { text-align: center; vertical-align: middle; }
        /* IF YOU WANT CLEANER LOOK, COMMENT FROM HERE */
        body { background-color: white; margin: 0; }
        .page { margin: 0; border: none; box-shadow: none; width: auto; min-height: 27.7cm; page-break-after: always; }
        footer { position: fixed; bottom: 1cm; left: 2cm; right: 2cm; }
        /* TO HERE */
        @media print {
            body { background-color: white; margin: 0; }
            .page { margin: 0; border: none; box-shadow: none; width: auto; min-height: 27.7cm; page-break-after: always; }
            footer { position: fixed; bottom: 1cm; left: 2cm; right: 2cm; }
        }
    </style>
</head>
<body onload="window.print()">
<div class="page">
    <header>
        <div class="logo">CORSA<span class="co-op">POLIBAN</span></div>
        <div class="doc-title">&nbsp;</div>
    </header>
    <div class="skill-section">
        <div class="skill-title">{{ $assessment->guide?->title  }}</div>
        <div class="skill-number">Skill Number {{ $assessment->guide?->skill_number }}</div>
    </div>

    <div class="info-grid">
        <div><strong>Full Name:</strong> {{ $assessment->assessee_name }}</div>
        <div><strong>No ID:</strong> {{ $assessment->assessee_no_id }}</div>
        <div><strong>Validation Date:</strong> {{ $assessment->scheduled_at?->isoFormat('D MMMM Y') ?? '-' }}</div>
        <div><strong>School:</strong> {{ $assessment->assessee_school }}</div>
    </div>

    <div class="task-section">
        <p><strong>PERFORMANCE TASK:</strong></p>
        <p>{!! $assessment->guide?->performance_task ?? '' !!}</p>
    </div>

    @foreach($assessment->tasks ?? $assessment->guide?->tasks as $taskGroup)
        <table>
            <thead>
            <tr>
                <th class="text-center" rowspan="2" style="width: 50%">Tasks</th>
                <th class="text-center" colspan="3">Completed</th>
            </tr>
            <tr>
                <th class="text-center" style="width: 35px">Yes</th>
                <th class="text-center" style="width: 35px">No</th>
                <th class="text-center" style="width: 35px">N/A</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="category" colspan="4">{{ $taskGroup['title'] }}</td>
            </tr>
            @foreach($taskGroup['child'] as $task)
                <tr>
                    <td>{{ $task['title'] }}</td>
                    <td class="text-center">@if(($task['status'] ?? '') === 'completed')<i>✓</i>@endif</td>
                    <td class="text-center">@if(($task['status'] ?? '') === 'not_completed')<i>✓</i>@endif</td>
                    <td class="text-center">@if(($task['status'] ?? '') === 'not_available')<i>✓</i>@endif</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="comments-section">
        <p><strong>General Comments</strong></p>
        <div class="comments-box">{{ $assessment->comment }}</div>
    </div>

    <div class="result-section">
        <div class="result-title">RESULT:</div>
        <div class="result-options">
            <div>
                <div class="checkbox">@if($assessment->result === 'competent')<i>✓</i>@endif</div>
                <span>COMPETENT</span>
            </div>
            <div>
                <div class="checkbox">@if($assessment->result === 'not_competent')<i>✓</i>@endif</div>
                <span>NOT YET COMPETENT</span>
            </div>
        </div>
        <div class="result-note">(please check (✓))</div>
    </div>

    <div class="signature-section">
        <div class="signature-block">
            <p><strong>Student:</strong></p>
            <div class="line"><span class="label">Name:</span><span class="value">{{ $assessment->assessee_name }}</span></div>
            <div class="line"><span class="label">Date:</span><span class="value">{{ $assessment->scheduled_at?->isoFormat('D MMMM Y') }}</span></div>
            <div class="line"><span class="label">Signature:</span></div>
        </div>
        <div class="signature-block">
            <p><strong>Assessor:</strong></p>
            <div class="line"><span class="label">Name:</span><span class="value">{{ $assessment->assessor?->name }}</span></div>
            <div class="line"><span class="label">Date:</span><span class="value">{{ $assessment->scheduled_at?->isoFormat('D MMMM Y') }}</span></div>
            <div class="line"><span class="label">Signature:</span></div>
        </div>
        <div class="signature-block">
            <p><strong>Supervisor:</strong></p>
            <div class="line"><span class="label">Name:</span><span class="value">&nbsp;</span></div>
            <div class="line"><span class="label">Date:</span><span class="value">&nbsp;</span></div>
            <div class="line"><span class="label">Signature:</span></div>
        </div>
        <div class="signature-block">
            <p><strong>Data Recorded:</strong></p>
            <div class="line"><span class="label">Name:</span><span class="value">&nbsp;</span></div>
            <div class="line"><span class="label">Date:</span><span class="value">&nbsp;</span></div>
            <div class="line"><span class="label">Signature:</span></div>
        </div>
    </div>

</div>

</body>
</html>
