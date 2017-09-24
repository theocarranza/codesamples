# codesamples
A few snippets of code from a Zend Framework 1 project to serve as an example of my coding skills.

This project had to solve two problems: Provide automation for internal and administrative processes and maintain digital elections to be held nation wide. The elections would be held for the national board of directors, organized in a slate to be elected as a single entity, individual state directors and regional city offie directors.

The main problem the system had to solve was, of course, the election. The process had to be safe and reliable, and security was a main concern. The system we designed had a formula to compute the votes, in which each vote depended o the previous vote and a few other parameters to be valid. A single invalid vote would cause the rest of the votes to be invalid as well, and the election would be considered null and void. One technical challenge we encoutered was to keep the system fast enough while performimg a set of heavy computations whenever a vote was cast. 

One concept I explored in this project is of lean controllers and models, as opposed to fat models and controllers with business login on their methods. The objective of this approach is to create controllers that are easy to read, understand and debug. The methods that are exposed to the frontend are 2 to 3 lines long, not considering the method header and signature. All methods perform a very specialized task, and return an object that informs the frontend if the operation was successful or not, with the relevant message appended to the object and ready to me displayed. When an exception is returned, the pertinent message is also included, otherwise the standard message for that particular use case is used instead.

That also meant we could break complex computations into smaller and simpler tasks, avoiding unessessary loops and nesting. 

This concept allows the developer to pinpoint exactly where the error is, and examine the pertinent business object to find and correct the problem. Also makes the project easier to understand and study.
