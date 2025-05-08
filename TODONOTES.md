# Notes on TODOs

As requested, my thoughts on possible solutions to the TODOS that I both did and didn't have chance to cover in the alloted 90 minutes, and some thoughts on why I approached the tasks in the order that I did.

My first sub-task was to initialise a git repository for the task itself; This can be found at `https://github.com/RowanGuyton/takehome-task-main` - a ZIP version of the repository can be downloaded from there - the repository has been made publicly accessible.

---
## index.php

### TODO A: Improve readability through refactoring and documentation.

- Mostly complete interms of refactoring, `index.php` and `App.php` were my first ports of call as they seemed the most relevant to the overall functionality of the app itself. My methodology is such that it is more important to attack the bigger problems first, even if they might take longer and be more difficult to solve.

* Given that `index.php` was a bit of a mess in terms of what needed to be done, I attempted to improve readibility to the best of my ability by moving all of the nested PHP functionality scattered throughout the `echoed` template body into the upper portion of the file itself, seperating functional logic from Markup presentation where possible. 

* If I had had more time, the best approach is that I likely would have separated the HTML structure even further into header, body, and footer templates, helping things like reusability.

* I also would have encapsulated logic for fetching and saving articles into methods or a controller for, again for purposes of reusability and modularity.

### TODO B: Review and correct HTML structure.

* There were some things that I missed along the way, for example - I should have added a proper `<!DOCTYPE html>`, `<html lang="en">`, `<meta charset="UTF-8">`, and a proper `<title>` to our `<head>` in a perfect world.

* I could have changed `<a class='submit-button'>` to something like `<button type="submit" class="submit-button">Submit</button>`, or something similar, for accessibility considerations.

* I could have double-backed and ensured all tags are properly closed and nested for thoroughness and completeness.

### TODO C: Security and performance considerations.

#### In terms of security and performance:

* **CSRF:** The most important aspect here in my opinion would have been to add a hidden CSRF token field somewhere and verify payloads on POST to prevent cross-site requests.

* **XSS:** Escape all dynamic output (`htmlspecialchars($title)`, `htmlentities($body)`). - I think I largely achieved this, though I may have missed a few given the time provided.

* **Performance:** The current word count functionality currently reads every file on each request, which leads to a poor runtime. This also leads into **TODO E** further down.

### TODO D: Dynamically generate the list of available articles.


* My solution to this was to replace hardcoded list with a loop that looped through available articles. I'm sure a more succint or optimal version (probaly including a separate function of some sort) could have been made, but the effect was achieved.

### TODO E: Word count performance issues and optimization.

**Notes:**

* Scanning every article and splitting on spaces is pretty poor, as it leads to O(N) per request.
* I could have fixed this by doing something like caching word counts , maybe by storing metadata (e.g., in a JSON index file or similar) and then only updating when an article is created/updated (ex by checking filemtime).

* **Some pseudo-code for this might look like:**

  ```php
  if (cache_exists && cache_timestamp >= last_modification) {
      return cached_count;
  }
  // else recalculate and update our cache here
  ```

### TODO F (optional): Unit test for App.php.

* I probably would have done something like create a Unit test that mocked a virtual filesystem, which would have allowed me to assert saving and fetching functionality:

**Example of what this might look like**

  ```php
  public function testSaveAndFetch() {
      $fs = vfsStream::setup('articles');
      $app = new App(vfsStream::url('articles'));
      $app->saveArticle('test', 'Hello');
      $this->assertSame('Hello', $app->fetch(['title' => 'test']));
  }
  ```

---

## App.php

### TODO: Improve the readability of this file through refactoring and documentation.

**Solution:**

* The first thing I did was to change ambiguous parameter names (`$ttl`, `$bd`) to ones that improved readability - (`$title`, `$body`).

* I also renamed `save` to `saveArticle`, `update` to `updateArticle`, `fetch` to `fetchArticle`, and `getListOfArticles` to `getCurrentArticles` for clarity.

* In a perfect world, I could have added something like a private `$basePath` property set by a constructor, instead of using globals.

* There was also scope to implement a sanitization helper to strip harmful characters and prevent malicious directory traversal had there been enough time, but it's understood that time constraints were part of the challenge.

* In theory I could have added some kind of exception (ex, `RuntimeException`) when file operations fail, instead of just failing silently.

* I added some basic documentation to the class class itself (which probably should have been expanded upon) and also to all methods.

---

## api.php

### TODO A: Improve the readability through refactoring and documentation.

* Given time constraints, I simple updated function call names in-line with those changed in `App.php`

* What I would have done is added a PHPDoc at the top of the file explaining the usage and parameters of the functionality.

* I could have also extracted routing logic for the `list`, `prefixsearch`, and `fetch` functionality.

### TODO B: Clean up the code so the routes and handlers are clearer and extendable.

* Something I could have done is define an associative array of handlers, keyed by mode - an example of this could look like:

  ```php
  $handlers = [
      'list'         => fn() => $app->getCurrentArticles(),
      'prefixsearch' => fn($q) => $app->searchByPrefix($q),
      'fetch'        => fn($t) => $app->fetch(['title' => $t]),
  ];
  ```

* We could probably also use a single lookup rather than the existing nested conditionals.

### TODO C: Identify performance concerns and comment on fixes.

* `getListOfArticles()` calls `scandir()` on every request.

* We could potentially fix this by caching the result in memory (with static variables) or external cache (Redis, for example).

* There is currently no HTTP caching headers.
* We could have approached this by adding `ETag` and `Cache-Control` headers for static content.

### TODO D: Potential security vulnerabilities.

* We need to ensure `title` is sanitized by allowing only alphanumeric, dashes, and underscores, helping prevent things like directory traversal.

* Because of XSS / JSON injection issues, we should always serve JSON with proper `Content-Type` and avoid embedding user input in HTML wherever possible.

* When it comes to displaying errors, it's probably better to suppress raw PHP errors and instead send generic 4xx/5xx messages, which is the approach I would have taken.

### TODO E: Document the code.

* I would have added inline comments above each major block explaining expected input, processing, and output.

---

## main.js

### TODO A: Refactor for readability and document.

* The ideal solution in my opinion would be to break down logic into named functions, for example - `initSubmitButton()`, `initAutocomplete()`, `debounce()`, `renderSuggestions()`, `fetchArticle()`, etc.

* I would have added JSDoc comments above each function outlining parameters and behavior.

### TODO B: Implement autocomplete with 200ms debounce.

* We could do something like add a generic `debounce(fn, delay)` utility.

* Then, on `titleInput.addEventListener('input')`, call `debouncedFetchSuggestions()`, or something along those lines (exact function names are debatable of course).

### TODO C: Populate textarea on suggestion selection.

* I think a solution to this could possibly be something like - on click of a suggestion `<li>`, fetch the article via our `fetchArticle('api.php?title=' + encodeURIComponent(value))` and then set `bodyTextarea.value`.

### TODO D: Show/hide autocomplete list on focus/blur.

* Here, we could add maybe `titleInput.addEventListener('focus', showList)`, and `blur`, with `setTimeout(hideList, 100)`, which would achieve the effect of allowing click events before hiding.

### TODO E: Support loading more suggestions on scroll.

* I would maybe attach something like: `suggestionBox.addEventListener('scroll')`; then, when scroll reaches near the bottom, we can call `fetchSuggestions(nextOffset)` to add more items.

### TODO F: Error handling and graceful degradation.

* I would have wrapped all `fetch` calls in `try/catch` blocks, as should be standard really; and then on error displayed a small `<div class="error">` message below the input.

* We could also implement something that would allow the user to type freely should the API fail as a backup.

---

## styles.css

### TODO A: Update styles to match design spec alignment.

* My simplest solution would be to apply Flexbox on our `.header` and `.page`:

**Might look something like this:**

  ```css
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .page {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  ```
* We could also centre the main form and preview pane with `margin: 0 auto`.

### TODO B: Ensure responsiveness on tablet/mobile.

* We can simple add some media queries here, which would likely get the job done effectively.

**THis would probabnly look something like this:**

  ```css
  @media (max-width: 768px) {
    .main { width: 100%; padding: 8px; }
  }
  @media (max-width: 480px) {
    .header, .main { padding: 4px; }
    input, textarea { width: 100%; }
  }
  ```

---

*Just want to say thank you again for your time, and I appreciate your consideration.*
*All the best, Rowan Guyton*
