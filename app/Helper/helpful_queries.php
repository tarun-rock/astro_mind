SELECT
    m.rank,CAST(m.scorer AS UNSIGNED) AS points,m.user_id, m.timing
FROM (
    SELECT d.user_id,d.scorer,  @rownum := @rownum + 1 AS rank, d.timing
    FROM (
        SELECT t.user_id, SUM(t.score) as scorer, SUM(t.time) as timing
        FROM contest_sessions t JOIN users ON users.id = t.user_id
        JOIN contest_quiz ON t.quiz_id = contest_quiz.id
        WHERE t.active = 1 AND contest_quiz.contest_id = 1  AND users.active = 1
        GROUP BY t.user_id
        ORDER BY scorer DESC, timing ASC, t.created_at ASC
    ) d,
        (SELECT @rownum := 0) r
) m
WHERE m.scorer > 0 AND m.user_id = 1
