=== Quiz Tool Lite ===
Contributors: alexfurr, lcw102 
Tags: academic, assessment, formative, quiz, questions
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 1.4.6

A light weight quiz tool aimed at academics wanting to create interactive learning content. Insert a single question or deploy an entire quiz.

== Description ==

This tool was originally written as we wanted to display an individual question on a page, along with feedback for formative assessment. However it also handles summative assessment if you wish to deploy an entire quiz.

**Features**

*   Single response (radio), multiple response (checkbox) and reflective question types (click to reveal)
*   Can Embed a single question on a wordpress post or page for formative assessment (no form posting)
*   Create multiple quizzes from your question 'pots' and deploy on a page for recording student scores
*   Adds an 'Insert Question' link to the editor toolbar
*   Uses shortcode
*   Export questions from one WP site to another
*   Display participants responses to previously submitted reflective questions on different pages
*   Quiz options for data windo available, max attempts, display feedback or not and max time between attempts

<div>
  <a href="https://www.efolio.soton.ac.uk/blog/alexfurr/quiz-tool-lite-demo/">Click here to view some example of the embedded formative questions</a>
</div>

This plugin is supported by the <a href="http://cite.soton.ac.uk/">University of Southampton</a>



== Installation ==

1.  Extract the zip file and copy contents in the wp-content/plugins/ directory of your WordPress installation
2.  Activate the Plugin from Plugins page.
== Frequently Asked Questions ==

**Does this do summative assessment?**

Yes the quiz can record answers if a quiz is deployed. If you deploy an individual question feedback is given but responses are not saved. This can be very useful for formative assessment.

**What options are there for quizzes?**

Currently you pick 'x' questions at random from a question pot. These questions are displayed randomly.
You can also pick dates that the quiz will be available between, the maximum attempts, whether to display feedback or not and maximum times between attempts

**What question types does this support?**

You can create single reponse and multiple response questions which covers around 95% of question types used in academia. You can also write 'reflective' questions where you ask users to think of an answer before getting them to click a button which displays an 'ideal answer'.

**How do I use this tool?**

To display a question.

1. Create a question pot (questions must be stored in a question pot)

2. Create your questions within the pot.

3. Each question has its own shortcode which can be pasted into a post or page OR you can use the 'in editor' button to select a specific question if you are unsure how to use shortcodes.

**To deploy a quiz.**

1.  Create a new quiz and choose to display 'x' questions at random from pot 1 and (for example) 'x' questions at random from pot 2.
2.  Copy the quiz shortcode onto a page.

The quiz saves the HIGHEST score and quizzes can be taken as often as possible, or you can limit attempts if you wish. It will only save the score if a user is logged in.

A results screen shows the highest score achieved by each registered user.

**Can anonymous users take a quiz**

Yes, but results won't be stored in the database

== Screenshots ==

1. A typical single choice question
2. A reflective question type (click to reveal)
3. The question pots page
4. Editing response options
5. Creating a quiz from question pots

== Changelog ==

= 1.4.6 =
* Bug fix to stop adding double BR tags

= 1.4.5 =
* Bug fix when creating a quiz with only one question

= 1.4.4 =
* Added options for copying questions to different pots

= 1.4.3 =
* Added the option to email users their score after test completion

= 1.4.2 =
* Now displays time until next possible quiz attempt e.g. 5 hours, 32 minutes

= 1.4.1 =
* Bug fix that upped the attempt count incorrectly under certain conditions

= 1.3 =
* Added loads more quiz options such as max attempts, time window, time between attmempts etc
* Added jquery datepicker from WP core and imported smooothness theme from google CDN

= 1.2 =
* Fixed minor bugs and imported jquery from google CDN

= 1.1.1 =
* Improved UI
* Added screen shots

= 1.1 =
* First release