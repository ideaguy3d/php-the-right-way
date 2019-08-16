<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 7/14/2018
 * Time: 12:43 PM
 */

// -- General --
//  1. strlen()
//  2. str_word_count()
//  3. substr()
// -- Searching Strings --
//  4. strstr()
//  5. strpos()
//  6. strrpos()
//  7. substr_count()
//  8. strpbrk()
//  9. str_replace()
// 10. substr_replace()
// 11.

$s1 = "Hello, I am a sentence, that is what I am";
$s2 = "Hello, world!";
$s3 = "It was the best of times, it was the worst of times";

$s2array = [
    substr($s2, -5, -1),
    strstr($s2, 'wor'),
    strstr($s2, 'wor', true),
];

echo "\n\n";
//echo "\n\n Sentence 1 has ".str_word_count($s1)." words \n\n";
echo " ---------- s2 answers ---------- \n";
var_dump($s2array);

//------------------------------------------
// heredoc prac, heredoc does have parsing
//------------------------------------------

$name = "YHWH";
$myHeredocStr = <<< MY_HEREDOC_PRAC
\n"'I cause to be', says "$name" (who is the "ONE" who 'Causes to be')"\n
MY_HEREDOC_PRAC;

//------------------------------------------
// nowdoc prac, does not have parsing
//------------------------------------------
$myNowdocStr = <<< 'MY_NOWDOC_PRAC'
\n"'I cause to be', says "$name" (who is the "ONE" who 'Causes to be')"\n
MY_NOWDOC_PRAC;


//echo $myHeredocStr;

//echo $myNowdocStr;
echo "\n\n";